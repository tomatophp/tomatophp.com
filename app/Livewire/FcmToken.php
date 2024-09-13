<?php

namespace App\Livewire;

use Detection\MobileDetect;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

class FcmToken extends Component
{

    #[On('fcm-token')]
    public function fcmToken(string $token)
    {
        $detect = new MobileDetect();
        $fcmToken = \App\Models\FcmToken::query()->where('token', $token)->first();
        if(!$fcmToken){
            $fcmToken = new \App\Models\FcmToken();
            $fcmToken->token = $token;
            $fcmToken->agent = $detect->isMobile() ? 'mobile' :'web';
            $fcmToken->save();
        }
    }

    #[On('fcm-notification')]
    public function fcmNotification(mixed $data)
    {
        $actions = [];
        if(isset($data['data'])){
            if(isset($data['data']['actions']) && is_object(json_decode($data['data']['actions']))){
                foreach (json_decode($data['data']['actions']) as $action){
                    $actions[] = Action::make($action->name)
                        ->color($action->color)
                        ->eventData($action->eventData)
                        ->icon($action->icon)
                        ->iconPosition($action->iconPosition)
                        ->iconSize($action->iconSize)
                        ->outlined($action->isOutlined)
                        ->disabled($action->isDisabled)
                        ->label($action->label)
                        ->url($action->url)
                        ->close($action->shouldClose)
                        ->size($action->size)
                        ->tooltip($action->tooltip)
                        ->view($action->view)
                        ->markAsUnread($action->shouldMarkAsUnRead??false)
                        ->markAsRead($action->shouldMarkAsRead??false);
                }
            }
        }

        if(isset($data['data']['sendToDatabase']) && $data['data']['sendToDatabase'] === true){
            Notification::make($data['data']['id'])
                ->title($data['data']['title'])
                ->actions($actions)
                ->body($data['data']['body'])
                ->icon($data['data']['icon'])
                ->iconColor($data['data']['iconColor'])
                ->color($data['data']['color'])
                ->duration($data['data']['duration'])
                ->send()
                ->sendToDatabase(auth()->user());
        }
        else {
            Notification::make($data['data']['id'])
                ->title($data['data']['title'])
                ->actions($actions)
                ->body($data['data']['body'])
                ->icon($data['data']['icon'])
                ->iconColor($data['data']['iconColor'])
                ->color($data['data']['color'])
                ->duration($data['data']['duration'])
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.fcm-token');
    }
}
