<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up(): void
    // {
    //     Schema::create('carts', function (Blueprint $table) {
    //         $table->id();
            
    //         // Sesuai Model lu: user_id
    //         $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
    //         // Sesuai Model lu: product_id
    //         $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
    //         // Sesuai Model lu: quantity
    //         $table->integer('quantity')->default(1);
            
    //         $table->timestamps();
    //     });
    // }

public function up(): void
{
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        
        // KITA PAKSA TIPE DATANYA SAMA PERSIS KAYAK DI ATAS
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('product_id');
        
        // BARU KITA SAMBUNG KABELNYA MANUAL
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        
        $table->integer('quantity')->default(1);
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};