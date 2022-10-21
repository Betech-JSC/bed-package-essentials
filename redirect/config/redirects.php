<?php

return [

    /*
    |
    | The redirect statuses that you will use in your application.
    | By default, the "301", "302" and "307" are defined.
    |
    */
    'statuses' => [
        302 => 'Normal (302)',
        301 => 'Permanent (301)',
        307 => 'Temporary (307)',
    ],

    /*
    |
    | Concrete implementation for the "redirect model".
    | To extend or replace this functionality, change the value below with your full "redirect model" FQN.
    |
    | Your class will have to (first option is recommended):
    | - extend the "Jamstackvietnam\Redirects\Models\Redirect" class
    | - or at least implement the "Jamstackvietnam\Redirects\Contracts\RedirectModelContract" interface.
    |
    | Regardless of the concrete implementation below, you can still use it like:
    | - app('redirect.model') OR app('\Jamstackvietnam\Redirects\Contracts\RedirectsModelContract')
    | - or you could even use your own class as a direct implementation
    |
    */
    'redirect_model' => \Jamstackvietnam\Redirects\Models\Redirect::class,

];
