<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRemovedStatusToJobPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify the status ENUM to include 'REMOVED'
        DB::statement("ALTER TABLE job_posts MODIFY COLUMN status ENUM('COMMITTED', 'UNPAID', 'UNPAID_TO_BE_PAID_DRAFT', 'UNPAID_TO_BE_PAID_DRAFT_REMOVED', 'DRAFT', 'ARCHIVED', 'REMOVED') DEFAULT 'DRAFT'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert any REMOVED status posts back to DRAFT before removing the ENUM value
        DB::statement("UPDATE job_posts SET status = 'DRAFT' WHERE status = 'REMOVED'");
        
        // Revert the ENUM back to original values
        DB::statement("ALTER TABLE job_posts MODIFY COLUMN status ENUM('COMMITTED', 'UNPAID', 'UNPAID_TO_BE_PAID_DRAFT', 'UNPAID_TO_BE_PAID_DRAFT_REMOVED', 'DRAFT', 'ARCHIVED') DEFAULT 'DRAFT'");
    }
}
