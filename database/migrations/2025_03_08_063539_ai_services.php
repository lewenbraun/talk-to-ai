<?php

declare(strict_types=1);

use App\Models\AiService;
use App\Enums\AiServiceEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url_api')->nullable();
            $table->timestamps();
        });

        AiService::insert([
            'id' => 1,
            'name' => AiServiceEnum::OLLAMA->value,
            'url_api' => 'http://localhost:11434',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_services');
    }
};
