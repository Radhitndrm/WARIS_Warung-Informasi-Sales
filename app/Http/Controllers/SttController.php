<?php

namespace App\Http\Controllers;

use App\Services\WhisperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SttController extends Controller
{
    public function __construct(
        protected WhisperService $whisper
    ) {}

    public function transcribe(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => 'required|file|max:10240',
        ]);

        $audio = $request->file('audio');
        $ext = $audio->getClientOriginalExtension() ?: 'webm';
        $path = $audio->storeAs(
            'stt',
            'audio_' . time() . '_' . uniqid() . '.' . $ext,
            'local'
        );

        $fullPath = Storage::disk('local')->path($path);

        $text = $this->whisper->transcribe($fullPath);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        if (empty($text)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengenali suara.',
            ]);
        }

        return response()->json([
            'success' => true,
            'text' => $text,
        ]);
    }
}
