<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('donasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kampanye_id')->constrained()->onDelete('cascade');
            $table->string('kampanye_nama');
            $table->string('user_id');
            $table->string('user_name');
            $table->decimal('jumlah', 15, 2);
            $table->text('pesan')->nullable();
            $table->string('metode_pembayaran');
            $table->enum('status', ['pending', 'success', 'failed', 'challenge'])->default('pending');
            $table->datetime('tanggal');
            $table->string('order_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('fraud_status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donasis');
    }
};
