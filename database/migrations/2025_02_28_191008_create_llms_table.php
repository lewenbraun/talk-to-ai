<?php

declare(strict_types=1);

use App\Models\AiService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('llms', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AiService::class);
            $table->string('name');
            $table->boolean('isLoaded')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llms');
    }
};
