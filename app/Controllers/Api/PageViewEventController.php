<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PageViewEventModel;
use CodeIgniter\API\ResponseTrait;
use Throwable;

class PageViewEventController extends BaseController
{
    use ResponseTrait;

    /**
     * POST /api/events/page-view
     * Expects a JSON body, e.g.:
     * {
     *   "event": "index",
     *   "url": "/apply/ogun",
     *   "referrer": "https://google.com",
     *   "userAgent": "...",
     *   "screen": "1920x1080",
     *   "language": "en-US",
     *   "timestamp": "2026-06-25T10:00:00.000Z",
     *   "session_id": "sess005",
     *   "app_name": "Remsana AI Programme",
     *   "email": "jane@example.com"   // optional, send when known
     * }
     */
    public function store()
    {
        try {
            $input = $this->request->getJSON(true);

            if (! is_array($input)) {
                return $this->fail('Invalid event data.', 400);
            }

            // event/url are the minimum needed for this to mean anything
            if (empty($input['event']) || empty($input['url'])) {
                return $this->fail('Invalid event data.', 422);
            }

            $model = new PageViewEventModel();

            $inserted = $model->insert([
                'event_type'        => $input['event'],
                'url'               => $input['url'],
                'referrer'          => $input['referrer'] ?? null,
                'user_agent'        => $input['userAgent'] ?? null,
                'screen_resolution' => $input['screen'] ?? null,
                'browser_lang'      => $input['language'] ?? null,
                'timestamp_event'   => $this->toMysqlDatetime($input['timestamp'] ?? null),
                'session_id'        => $input['session_id'] ?? null,
                'app_name'          => $input['app_name'] ?? null,
                'email'             => $input['email'] ?? null,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);

            if (! $inserted) {
                return $this->fail('Could not record event.', 500);
            }

            return $this->respondCreated(['message' => 'Event recorded.']);
        } catch (Throwable $e) {
            log_message('error', 'PageViewEventController::store - ' . $e->getMessage());

            return $this->fail('Could not record event.', 500);
        }
    }

    /**
     * React sends an ISO timestamp (new Date().toISOString()), e.g.
     * "2026-06-25T10:00:00.000Z" — convert to MySQL DATETIME format.
     */
    private function toMysqlDatetime(?string $isoTimestamp): ?string
    {
        if (empty($isoTimestamp)) {
            return null;
        }

        try {
            return date('Y-m-d H:i:s', strtotime($isoTimestamp));
        } catch (Throwable $e) {
            return null;
        }
    }
}
