<?php

namespace App\Http\Livewire;

use App\Models\Record;
use App\Models\Shift;
use Illuminate\Support\Collection;
use Livewire\Component;

class TemplatesBoard extends Component
{

    public $recordClickEnabled;

    public function mount($recordClickEnabled = false,$extras = [])
    {
        $this->recordClickEnabled = $recordClickEnabled ?? false;

        $this->afterMount($extras);
    }

//    public function hydrate(){
//        $records = $this->records();
//    }

    public function afterMount($extras = [])
    {
        //
    }

    public function statuses() : Collection
    {
        return collect([
            [
                'id' => '0',
                'title' => 'Empleados libres',
            ],
            [
                'id' => '1',
                'title' => 'Lunes',
            ],
            [
                'id' => '2',
                'title' => 'Martes',
            ],
            [
                'id' => '3',
                'title' => 'Miercoles',
            ],
            [
                'id' => '4',
                'title' => 'Jueves',
            ],
            [
                'id' => '5',
                'title' => 'Viernes',
            ],
            [
                'id' => '6',
                'title' => 'Sábado',
            ],
            [
                'id' => '7',
                'title' => 'Domingo',
            ],
        ]);
    }

    public function records() : Collection
    {
        return Record::all();
    }

    public function shifts() : Collection
    {
        return Shift::all();
    }


    public function isRecordInStatus($record, $status)
    {
        return $record['status'] == $status['id'];
    }

    public function onStatusSorted($recordId, $statusId, $orderedIds)
    {
        //
    }

    public function onStatusChanged($recordId, $statusId, $fromOrderedIds, $toOrderedIds)
    {
        $record = Record::find($recordId);
        $record->status = $statusId;
        $record->save();

    }

    public function onRecordClick($recordId)
    {
        //
    }


    public function render()
    {
        $statuses = $this->statuses();

        $records = $this->records();

        $shifts = $this->shifts();

        $statuses = $statuses
            ->map(function ($status) use ($records) {
                $status['group'] = $this->id;
                $status['statusRecordsId'] = "{$this->id}-{$status['id']}";
                $status['records'] = $records
                    ->filter(function ($record) use ($status) {
                        return $this->isRecordInStatus($record, $status);
                    });

                return $status;
            });

        return view('board')
            ->with([
                'records' => $records,
                'statuses' => $statuses,
                'shifts' => $shifts
            ]);
    }
}
