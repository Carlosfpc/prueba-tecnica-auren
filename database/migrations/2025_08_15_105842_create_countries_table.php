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
        Schema::create('countries', function (Blueprint $table) {
            $table->string('cca3', '3')->primary();

            $table->string('name_common');
            $table->string('name_official');

            // Geographical data. Indexed for faster filtering.
            $table->string('region')->index();

            // Some countries might not have a subregion.
            $table->string('subregion')->nullable();

            // Capital city. Nullable as some territories may not have one.
            $table->string('capital')->nullable();

            $table->unsignedBigInteger('population');
            $table->decimal('area', 10, 2);

            // Flag data. Emojis can be multi-byte. PNG is a URL.
            $table->string('flag_emoji', 16)->nullable();
            $table->string('flag_png')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
