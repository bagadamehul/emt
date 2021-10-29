<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalPrequelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_prequel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->text('qry')->nullable();
            $table->string('type')->nullable();
            $table->integer('approved_by')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->enum('status',['0','1','2'])->default(0);           
            $table->text('remarks')->nullable();
            $table->integer('qry_count')->default('0');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
