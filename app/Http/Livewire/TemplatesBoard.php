<?php

namespace App\Http\Livewire;

use App\Models\Record;
use Illuminate\Support\Collection;
use Livewire\Component;

class TemplatesBoard extends Component
{
    /*
    *propiedad relacionada con los registros de la DB
    */
    public $records;

    /**
     * Listener para remover un registro de la DB
     */
    protected $listeners = ['recordRemoved' => 'recordRemoved'];

    /**
     * Undocumented function
     *
     * @param boolean $recordClickEnabled
     * @param array $extras
     * @return void
     */
    public function mount($recordClickEnabled = false,$extras = [])
    {
        $statuses = [1,2,3];

        $this->recordClickEnabled = $recordClickEnabled ?? false;

        $this->afterMount($extras);

        $this->records = Record::all();
    }

    // public function hydrate(){
    //     $records = $this->records();
    // }

    public function afterMount($extras = [])
    {
        //
    }
    /**
     * Collection de status para pruebas, podría sacarse de una tabla de la DB
     *
     * @return Collection
     */
    public function statuses() : Collection
    {
        return collect([
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

    /**
     * Collection de turnos para pruebas, podría sacarse de una tabla de la DB
     *
     * @return Collection
     */
    public function turnos() : Collection
    {
        return collect([
            [
                'id' => '1',
                'title' => 'Mañana',
            ],
            [
                'id' => '2',
                'title' => 'Tarde',
            ],
            [
                'id' => '3',
                'title' => 'Noche',
            ],
            [
                'id' => '4',
                'title' => 'Noche_Partido',
            ]
        ]);
    }

    /**
     * Collection de empleados para pruebas, podría sacarse de una tabla de la DB
     *
     * @return Collection
     */
    public function empleados() : Collection
    {
        //traemos los empleados de la base de datos
        //en esta versión simplemente tenemos un array de 15 empleados
        //para generar el primer draggable de la funcionalidad

        return collect([
            [
                'id' => '1',
                'empleado' => 'Dr. Quinten Schinner',
            ],
            [
                'id' => '2',
                'empleado' => 'Eriberto Roob',
            ],
            [
                'id' => '3',
                'empleado' => 'Dr. Victoria West MD',
            ],
            [
                'id' => '4',
                'empleado' => 'Rosa Macejkovic',
            ],
            [
                'id' => '5',
                'empleado' => 'Prof. Andrew Blanda',
            ],
            [
                'id' => '6',
                'empleado' => 'Jaylin Romaguera',
            ],
            [
                'id' => '7',
                'empleado' => 'Prof. Narciso Veum',
            ],
            [
                'id' => '8',
                'empleado' => 'Micaela Monahan',
            ],
            [
                'id' => '9',
                'empleado' => 'Dr. Arnold Hirthe',
            ],
            [
                'id' => '10',
                'empleado' => 'Ms. Antoinette Paucek',
            ],
            [
                'id' => '11',
                'empleado' => 'Morgan Schultz',
            ],
            [
                'id' => '12',
                'empleado' => 'Rosalyn Block Jr.',
            ],
            [
                'id' => '13',
                'empleado' => 'Ms. Antoinette Paucek',
            ],
            [
                'id' => '14',
                'empleado' => 'Phyllis Lockman',
            ],
            [
                'id' => '35',
                'empleado' => 'Magnus Predovic',
            ],
        ]);
    }

    /**
     * Get all the records from de DB
     *
     * @return Collection
     */
    public function getRecords() : Collection
    {
        return $this->records = Record::all();
    }

    /**
     * Función que se invoca cuando hay un cambio en la vista
     * La vista manda el id del turno y el empleado
     * La función chequea en la DB si existe ese registro
     * si no existe lo crea
     * e invoca la función para recuperar los registros de la DB.
     * Redirecciona a la misma página para evitar repeticiones de empleados en los turnos
     *
     * @param string $toId
     * @param string $empleado
     * @return void
     */
    public function onStatusChanged ( string $toId, string $empleado )
    {
        $record = new Record();
        $record->status = $toId;
        $record->title = $empleado;
        $recordAnt = Record::where('status', 'like', $toId)
                            ->where('title', 'like', $empleado)
                            ->first();
        if( !$recordAnt ){
            $record->save();
            session()->flash('message', 'Trabajador: '.strtoupper($empleado).' añadido a turno: '.strtoupper(str_replace ( "_", ' ', $record->status)));
        }
        $this->getRecords();

        return redirect(request()->header('Referer'));
    }

    /**
     * Función para remover registros de la DB
     * La vista llama a la función pasandole el turno y el empleado a remove
     * Comprueba que existan
     * y si existe lo remueve
     * Redirecciona a la misma página para evitar repeticiones de empleados en los turnos
     *
     * @param string $turno
     * @param string $empleado
     * @return void
     */
    public function removeFromDB( string $turno, string $empleado ){

        $record = Record::where('status', 'like', $turno)
                            ->where('title', 'like', $empleado)
                            ->first();
        if($record){
            $record->delete();
            session()->flash('message', 'Trabajador: '.strtoupper($empleado).' eliminado del turno: '.strtoupper(str_replace ( "_", ' ', $record->status)));
        }


        $this->getRecords();
        $this->mount();

        return redirect(request()->header('Referer'));
    }

    /**
     * Generamos la vista pasandole: empleados, statuses y turnos y registros de la DB
     *
     * @return void
     */
    public function render()
    {

        //pasamos a la vista los siguientes parametros:
        $empleados = $this->empleados();
        $statuses = $this->statuses();
        $turnos = $this->turnos();
        $records = $this->getRecords();

        $statuses = $statuses
            ->map(function ($status) use ($turnos) {
                $status['group'] = $this->id;
                $status['statusTurnoId'] = "{$this->id}-{$status['id']}";
                // $status['turnos'] = $turnos
                //     ->filter(function ($turno) use ($status) {
                //         return $this->isTurnoInStatus($turno, $status);
                //     });

                return $status;
            });

            return view('board', [
                'records' => $records,
                'statuses' => $statuses,
                'empleados' => $empleados,
                'turnos' => $turnos
            ]);

    }
}
