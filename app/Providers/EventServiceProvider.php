<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(function (RequestHandled $event) {
            $request = $event->request;
            $response = $event->response;
            $row = [
                $request->method(),
                $request->path(),
                json_encode($request->input()),
                json_encode($response->getContent()),
                date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])];
            $table_name = 'request_log_'.date('Ymd');
            if(!Schema::hasTable($table_name)) {
                Schema::create($table_name, function (Blueprint $table) {
                    $table->id();
                    $table->string('method');
                    $table->string('path');
                    $table->string('data');
                    $table->text('response');
                    $table->timestamps();
                });
            }
            DB::insert('insert into '.$table_name.' (method, path,data,response,created_at) values (?, ?, ?, ?, ?)', $row);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
