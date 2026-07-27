<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('html_template');
            $table->text('css_styles')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->text('company_info')->nullable();
            $table->string('footer_text')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
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
        Schema::dropIfExists('quotation_templates');
    }
}
