<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class WhisperService
{
    protected string $binary;
    protected string $model;

    public function __construct()
    {
        $this->binary = config('services.whisper.binary', '/opt/homebrew/bin/whisper-cli');
        $this->model = config('services.whisper.model', '/opt/homebrew/Cellar/whisper-cpp/1.8.5/share/whisper-cpp/ggml-tiny.bin');
    }

    public function transcribe(string $audioPath): string
    {
        if (!file_exists($audioPath)) {
            Log::error('Audio file not found', ['path' => $audioPath]);
            return '';
        }

        $outputDir = dirname($audioPath);
        $baseName = pathinfo($audioPath, PATHINFO_FILENAME);
        $wavPath = "{$outputDir}/{$baseName}.wav";

        if (pathinfo($audioPath, PATHINFO_EXTENSION) !== 'wav') {
            $convert = new Process([
                'ffmpeg', '-y', '-i', $audioPath, '-ar', '16000', '-ac', '1', '-c:a', 'pcm_s16le', $wavPath,
            ]);
            $convert->setTimeout(30);
            $convert->run();

            if (!$convert->isSuccessful()) {
                Log::error('FFmpeg conversion failed', [
                    'input' => $audioPath,
                    'output' => $wavPath,
                    'error' => $convert->getErrorOutput(),
                ]);
                return '';
            }
        } else {
            $wavPath = $audioPath;
        }

        $outputPath = "{$outputDir}/{$baseName}";

        $process = new Process([
            $this->binary,
            '-m', $this->model,
            '-f', $wavPath,
            '-l', 'id',
            '-otxt',
            '-of', $outputPath,
        ]);

        $process->setTimeout(120);
        $process->run();

        $txtFile = $outputPath . '.txt';
        $transcription = '';

        if (file_exists($txtFile)) {
            $transcription = trim(file_get_contents($txtFile));
            unlink($txtFile);
        }

        foreach (['vtt', 'srt', 'csv', 'json', 'tsv'] as $ext) {
            $f = $outputPath . '.' . $ext;
            if (file_exists($f)) unlink($f);
        }

        if ($wavPath !== $audioPath && file_exists($wavPath)) {
            unlink($wavPath);
        }

        if (!$process->isSuccessful()) {
            Log::error('Whisper processing failed', [
                'exit_code' => $process->getExitCode(),
                'error' => $process->getErrorOutput(),
            ]);
            return '';
        }

        return $transcription;
    }
}
