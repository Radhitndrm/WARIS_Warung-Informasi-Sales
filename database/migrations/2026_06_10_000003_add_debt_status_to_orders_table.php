<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `orders` CHANGE `status` `status` ENUM('pending', 'paid', 'cancelled', 'debt') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `orders` CHANGE `status` `status` ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending'");
    }
};
