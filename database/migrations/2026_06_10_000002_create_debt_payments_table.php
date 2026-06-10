<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->cascadeOnDelete();
            $table->integer('amount');
            $table->enum('method', ['cash', 'qris']);
            $table->enum('status', ['pending', 'success', 'failed'])->default('success');
            $table->string('midtrans_id')->nullable();
            $table->string('snap_token')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
    }
};
