<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Trigger sync current_assignee_id
        DB::unprepared('
            DROP TRIGGER IF EXISTS `trg_sync_current_assignee_after_assignment`;
            CREATE TRIGGER `trg_sync_current_assignee_after_assignment`
            AFTER INSERT ON `ticket_assignments`
            FOR EACH ROW
            BEGIN
                UPDATE `tickets`
                SET `current_assignee_id` = NEW.assigned_to_staff_id,
                    `updated_at` = CURRENT_TIMESTAMP
                WHERE `id` = NEW.ticket_id;
            END;
        ');

        // 2. Trigger calculate sla_deadline
        DB::unprepared('
            DROP TRIGGER IF EXISTS `trg_tickets_calculate_sla_before_insert`;
            CREATE TRIGGER `trg_tickets_calculate_sla_before_insert`
            BEFORE INSERT ON `tickets`
            FOR EACH ROW
            BEGIN
                DECLARE category_sla INT;
                
                IF NEW.sla_deadline IS NULL THEN
                    SELECT `sla_hours` INTO category_sla 
                    FROM `ticket_categories` 
                    WHERE `id` = NEW.category_id;
                    
                    IF category_sla IS NOT NULL THEN
                        SET NEW.sla_deadline = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL category_sla HOUR);
                    ELSE
                        SET NEW.sla_deadline = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 24 HOUR);
                    END IF;
                END IF;
            END;
        ');

        // 3. Trigger update & reset timestamps (resolved_at, closed_at) + recalculate sla_deadline on REOPENED
        DB::unprepared("
            DROP TRIGGER IF EXISTS `trg_tickets_update_timestamps_before_update`;
            CREATE TRIGGER `trg_tickets_update_timestamps_before_update`
            BEFORE UPDATE ON `tickets`
            FOR EACH ROW
            BEGIN
                DECLARE category_sla INT;

                IF NEW.status = 'REOPENED' AND OLD.status != 'REOPENED' THEN
                    SELECT `sla_hours` INTO category_sla 
                    FROM `ticket_categories` 
                    WHERE `id` = NEW.category_id;

                    SET NEW.resolved_at = NULL;
                    SET NEW.closed_at = NULL;
                    SET NEW.sla_deadline = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL IFNULL(category_sla, 24) HOUR);
                END IF;

                IF NEW.status = 'RESOLVED' AND OLD.status != 'RESOLVED' THEN
                    SET NEW.resolved_at = CURRENT_TIMESTAMP;
                END IF;

                IF NEW.status = 'CLOSED' AND OLD.status != 'CLOSED' THEN
                    SET NEW.closed_at = CURRENT_TIMESTAMP;
                END IF;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `trg_sync_current_assignee_after_assignment`');
        DB::unprepared('DROP TRIGGER IF EXISTS `trg_tickets_calculate_sla_before_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `trg_tickets_update_timestamps_before_update`');
    }
};
