<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class LikeController extends Controller
{
    public function index($id)
    {
        $user_id = Auth::user()['id'];
        $event = Event::find($id);

        if(!$event)
        {
            return Response::json(['success' => false, 'error' => 'Evento não encontrado.']);
        }

        else
        {
            $like = $event->likes()->where('user_id', $user_id)->first();;

            if(!$like)
            {
                Like::create([
                    'user_id' => $user_id,
                    'event_id' => $id
                ]);

                return Response::json(['success' => true, 'message' => 'Favoritado']);
            }

            else
            {
                $like->delete();

                return Response::json(['success' => true, 'message' => 'Desfavoritado']);
            }
        }

    }
}
