<?php

namespace JamstackVietnam\Contact\Traits;

use Illuminate\Support\Facades\Notification;
use JamstackVietnam\Contact\Notifications\CommonNotification;

trait HasNotification
{
    public static function bootHasNotification()
    {
        static::created(function ($model) {
            if (request()->route() === null) return;

            $emails = explode(',', settings()->group('notification')->get('notification_to', null));
            $data['mail_title'] = config('contact.message.new_contact');

            if (method_exists($model, 'transformEmail')) {
                $data = array_merge($data, $model->transformEmail());
            }

            foreach($emails as $email)
            {
                Notification::route('mail', $email)
                    ->notify(new CommonNotification($data));
            }

            // send customer
            if (method_exists($model, 'transformEmailDetails')) {
                $data = $model->transformEmailDetails();
            } else {
                $data = $model->data;
            }

            $data['mail_title'] = config('contact.message.success_form');
            if (isset($data['Email'])) {
                $emailTo = $data['Email'];

                Notification::route('mail', $emailTo)
                    ->notify(new CommonNotification($data));
            }
        });
    }
}
