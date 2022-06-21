<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class EventController extends Controller
{
    // Próximos eventos
    public function index($method)
    {
        $model = new Event();
        $code = [
            'next' => $model->nextEvents(),
            'today' => $model->todayEvents(),
            'week' => $model->weekEvents(),
            'month' => $model->monthEvents(),
            'year' => $model->yearEvents(),
        ];

        $data = EventResource::collection($code[$method]);

        if($data->isEmpty())
            return Response::json(['success' => false, 'error' => 'Não foi possível encontrar eventos.'], 404);
        else
            return Response::json(['success' => true, 'events' => $data], 200);
    }

    public function show($id)
    {
        $query = Event::query()->where('id', $id)->get();
        $data = EventResource::collection($query);
        if($data->isEmpty())
            return Response::json(['success' => false, 'error' => 'Evento não encontrado.'], 404);
        else
            return Response::json(['success' => true, 'events' => $data], 200);
    }

    public function store(EventRequest $request)
    {
        $req = $request->all();
        // Manipula imagem do evento
        if($request->hasFile('image'))
        {
            $file = $req['image'];
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/events/', $filename);
            $req['image'] = $filename;
        }

        $req['user_id'] = Auth::user()->id;
        $event = Event::create($req);

        return new EventResource($event);
    }

    public function update(EventRequest $request, $id)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $id)->first();

        if($event) {
            $req = $request->all();
            if($request->hasFile('image'))
            {
                $file = $req['image'];
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/events/', $filename);
                $req['image'] = $filename;
            }
            $event->update($req);

            return new EventResource($event);
        } else {
            return Response::json(['success' => false, 'error' => 'Você não tem autorização para essa ação.'], 403);
        }
    }

    public function destroy($id)
    {
        $user_id = Auth::user()['id'];
        $event = Event::query()->where('id', $id)->first();

        if ($event)
        {
            if ($event['user_id'] == $user_id)
            {
                $event->delete();
                return Response::json(['success' => true, 'message' => 'Evento deletado com sucesso.'], 200);
            }
            else
            {
                return Response::json(['success' => false, 'error' => 'Você não possui autorização para essa ação.'], 403);
            }
        }
        else
        {
            return Response::json(['success' => false, 'error' => 'Evento não encontrado.'], 404);
        }
    }

    private function success(array $array, string $string, int $int)
    {
    }
}
