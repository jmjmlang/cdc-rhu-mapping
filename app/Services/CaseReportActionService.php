<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CaseReportAction;
use App\Models\User;

class CaseReportActionService
{
    public function create(array $validated, User $author): CaseReportAction
    {
        $action = CaseReportAction::create([
            ...$validated,
            'user_id' => $author->id,
            'status'  => 'open',
        ]);

        $this->log($author, $action, 'case_action_created');

        return $action;
    }

    public function complete(CaseReportAction $action, User $author): CaseReportAction
    {
        $action->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        $this->log($author, $action, 'case_action_completed');

        return $action;
    }

    private function log(User $author, CaseReportAction $action, string $event): void
    {
        ActivityLog::create([
            'user_id'      => $author->id,
            'action'       => $event,
            'subject_type' => CaseReportAction::class,
            'subject_id'   => $action->id,
            'properties'   => [
                'report_id'    => $action->case_report_id,
                'display_name' => preg_replace('/\s+(\S)\S*$/', ' $1.', $author->name),
                'action_type'  => $action->action_type,
                'audience'     => $action->audience,
                'priority'     => $action->priority,
            ],
        ]);
    }
}
