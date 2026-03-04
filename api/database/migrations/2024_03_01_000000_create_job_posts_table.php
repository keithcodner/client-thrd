<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();

            $table->string('title');
            $table->string('seo_title')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('body');
            $table->string('image')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('slug')->nullable();
            $table->string('slug_trans')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->enum('status', ['PUBLISHED', 'DRAFT', 'PENDING'])->default('DRAFT');
            $table->boolean('featured')->default(false);
            $table->string('position')->nullable();
            $table->string('company_name')->nullable();
            $table->longText('job_description')->nullable();
            $table->enum('employer_type', [
                'full-time', 'part-time', 'contractor', 'temporary',
                'internship', 'per diem', 'volunteer', 'onsite'
            ])->nullable();

            $table->string('budget')->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->enum('payment_frequency', ['milestone', 'hourly', 'one-time'])->nullable();

            $table->enum('primary_tag', [
                'software development', 'customer service', 'sales', 'marketing',
                'design', 'frontend', 'backend', 'legal', 'Quality assurance',
                'testing', 'non-tech', 'other', 'JavaScript'
            ])->nullable();
            $table->json('secondary_tags')->nullable();
            $table->json('skills')->nullable();

            $table->enum('location_type', ['remote', 'on-site', 'hybrid'])->nullable();
            $table->string('location_restriction')->nullable();

            $table->string('location_country')->nullable();
            $table->string('location_state_province')->nullable();
            $table->string('location_city')->nullable();
            $table->string('location_zip_postal')->nullable();
            $table->string('location_long')->nullable();
            $table->string('locaiton_lat')->nullable();

            $table->boolean('show_company_logo')->default(false);
            $table->boolean('highlight_company_with_color')->default(false);
            $table->boolean('highlight_company')->default(false);
            $table->string('brand_color', 20)->nullable();

            $table->boolean('base_post')->default(true);
            $table->boolean('email_blast_job')->default(false);
            $table->boolean('auto_match_applicant')->default(false);
            $table->boolean('create_qr_code')->default(false);
            $table->boolean('highlight_post')->default(false);
            $table->boolean('sticky_note_24_hour')->default(false);
            $table->boolean('sticky_note_week')->default(false);
            $table->boolean('sticky_note_month')->default(false);
            $table->boolean('geo_lock_post')->default(false);

            $table->longText('how_to_apply')->nullable();
            $table->string('apply_url')->nullable();
            $table->string('apply_email_address')->nullable();

            $table->string('company_twitter')->nullable();
            $table->string('company_email')->nullable();
            $table->string('invoice_email')->nullable();
            $table->text('invoice_address')->nullable();
            $table->text('invoice_notes_po_box_number')->nullable();

            $table->text('feedback_box')->nullable();
            $table->boolean('pay_later')->default(0);
            $table->json('benefits')->nullable();
            $table->unsignedBigInteger('views')->nullable();
            $table->unsignedBigInteger('clicks')->nullable();

            $table->timestamp('expires_at')->nullable()->default(null);

            $table->timestamps();

            // Optional foreign key constraints (uncomment if applicable)
            // $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_posts');
    }
}
