<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->comment('版本號 e.g. 1.0.0');
            $table->timestamp('deployed_at')->nullable()->comment('部署時間');
            $table->string('token', 12)->unique()->comment('12位 release token');
            $table->string('git_sha', 40)->nullable()->comment('Git commit SHA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};