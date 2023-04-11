<?php

namespace JamstackVietnam\Contact\Traits;

use Illuminate\Support\Facades\Notification;
use JamstackVietnam\Contact\Notifications\CommonNotification;

trait HasNotification
{
    public static function bootHasNotification()
    {
        static::created(function ($model) {
            if (request()->route() === null || !config('contact.send_email_default', true)) return;

            if ($model->status === 'IS_SPAM') {
                $emails = [config('contact.mail_feedback', null)];

                $data['mail_title'] = config('contact.message.new_contact');

                $data = array_merge($data, $model->data);

                $data['reverse'] = route('admin.contacts.reverse', ['id' => $model->id]);

                foreach($emails as $email)
                {
                    Notification::route('mail', $email)
                        ->notify(new CommonNotification($data));
                }
            }
            else {
                $emails = explode(',', notification_to());

                $data['mail_title'] = config('contact.message.new_contact');

                if (method_exists($model, 'transformEmail')) {
                    $data = array_merge($data, $model->transformEmail());
                }

                foreach($emails as $email)
                {
                    Notification::route('mail', $email)
                        ->notify(new CommonNotification($data));
                }
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
