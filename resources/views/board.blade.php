<div>
    <div class="absolute w-full p-2 mt-2">
        @if (session()->has('message'))
                <div class="p-2" id="alert-message">
                    <div class="inline-flex items-center p-2 text-base leading-none text-green-400 bg-white rounded-full drop-shadow-2xl text-teal">
                        <span class="inline-flex items-center justify-center h-6 px-3 text-white bg-green-600 rounded-full">
                              {{ session('message') }}
                        </span>
                    </div>
                  </div>
        @endif
    </div>
    <style>
        .ghost {
            opacity: 0.4;
            background-color: red;
        }
        .my-handle {
            cursor: move;
            cursor: -webkit-grabbing;
        }
    </style>
    <div class="flex w-full h-full space-x-6 overflow-x-auto">
        <div class="flex flex-row justify-start flex-1 h-full">
            <!-- generamos un div draggable para clonar por cada empleado -->
            <div class="flex flex-col py-2 mx-2 my-2 text-center bg-gray-300">
                <span class="font-bold">EMPLEADOS:</span>
                @foreach($empleados as $empleado)
                <div class="flex flex-col px-2 m-2 border-2 rounded" id="Empleado_{{ $empleado['id'] }}">
                    <div class="flex flex-row justify-between p-2 m-2 text-sm align-center" id="{{ $empleado['empleado'] }}">
                        <span class="p-2 m-2 my-handle">::</span>
                        <p class="p-2 m-2 font-bold cursor-pointer">{{ $empleado['empleado'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- generamos un div para cada dia de la semana -->
            <div class="flex flex-row justify-start py-2 my-2 bg-gray-300">
                @foreach($statuses as $status)
                    <div class="flex flex-col justify-start p-2 mx-0 my-2 text-center text-gray-100 bg-gray-600 border" wire:model="records">
                        <span class="m-2 font-bold">{{ $status['title']}}</span>
                        <div
                            id="{{ $status['title'] }}"
                            @if($recordClickEnabled)
                            wire:click="onRecordClick('{{ $record['id'] }}')"
                            @endif
                            class="flex flex-col p-2 my-4 border rounded shadow">
                                <!-- generamos un div droppable para cada turno -->
                                @foreach($turnos as $turno)
                                <div
                                    id="{{ $status['title'] }}_{{ $turno['title'] }}"
                                    data-empleados=""
                                    @if($recordClickEnabled)
                                    wire:click="onRecordClick('{{ $record['id'] }}')"
                                    @endif
                                    class="flex flex-col py-2 my-4 bg-gray-200 border-2 border-indigo-400 border-solid rounded shadow">
                                        <span class="text-gray-600">{{ $turno['title'] }}</span>
                                </div>
                                        @foreach ($records as $index => $record )
                                        <div wire:key="{{ $record->id }}">
                                            @if( $record->status ==  $status['title']."_".$turno['title'] )
                                                <div class="flex flex-col py-2 border-2 rounded" id="{{ $record->title }}">
                                                    <div class="flex flex-row justify-between p-2 text-sm text-gray-200" data-empleado="{{ $record['title'] }}">
                                                        <p class="p-2 mx-2">
                                                            {{ $record->title }}
                                                        </p>
                                                        <span
                                                        onclick="removeItem(event, {{ $status['title'].'_'.$turno['title']}} )")
                                                        id="remove_{{ $record['title'] }}"
                                                        class="py-2 font-bold text-red-400 cursor-pointer">
                                                        X
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @endforeach
                                @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
    <div>
        <script>
            window.onload = () => {
                @foreach($empleados as $empleado)
                // creamos un elemento draggable por cada empleado
                // vinculado a los elementos droppables de la lista de la semana con el group
                Sortable.create(document.getElementById('Empleado_{{ $empleado['id'] }}'), {
                    group: {
                        name: 'shared',
                        // pull: 'clone',
                        put: false,
                        animation: 100,
                        ghostClass: 'bg-indigo-100',
                        swap: true, // Enable swap plugin
                        swapClass: 'font-bold', // The class applied to the hovered swap item
                        animation: 150,
                        swapThreshold: 0,
                        sort: false,
                        handle: '.handle', // handle's class
                    },
                    // setData: function (dataTransfer, dragEl) {
                    //     dataTransfer.setData('id', dragEl.id);
                    // },
                    // al seleccionar el elemento le añadimos una clase para diferenciarlo del resto de elementos del DOM
                    onChoose: function (/**Event*/evt) {
                        evt.oldIndex;  // element index within parent
                        // console.log(evt.item.parentElement);
                        evt.item.classList.add('text-red-600');
                    },
                    // al finalizar el drag le quitamos el cursor-pointer
                    // y llamamos a la funcion de Livewire para qeu nos lo añada a la DB
                    onEnd: function (evt) {
                        evt.preventDefault();
                        evt.item.classList.remove('cursor-pointer');
                        let parentId = evt.item.parentElement.id;
                        @this.call('onStatusChanged', evt.to.id, evt.item.id );
                    },
                });
                @endforeach

                @foreach($statuses as $status)
                    @foreach($turnos as $turno)
                    Sortable.create(document.getElementById('{{ $status['title']}}_{{ $turno['title'] }}'), {
                        ghostClass: "bg-gray-400",
                        group: {
                            name: 'shared',
                            put: true,
                            pull: false,
                            animation: 100,
                            // ghostClass: 'bg-indigo-100',
                            swapThreshold: 0,
                            sort: true,
                        },
                        setData: function (dataTransfer, dragEl) {
                            dataTransfer.setData('id', dragEl.id);
                        },
                        onEnd: function (evt) {
                        evt.preventDefault();
                        }
                    });
                    @endforeach
                @endforeach

                // creamos una constante para eliminar el mensaje de alerta de la session a los dos segundos
                const alert = document.getElementById('alert-message');
                setTimeout(() => {
                    alert.classList.add('hidden');
                }, 2000);
            }

            // creamos una constante para pasarle a la funcion removeFromDB de Livewire
            // pasandole los datos del truno y el empleado
            const removeItem = ( event, turno ) => {
                    let e = event.target.parentElement;
                    @this.call('removeFromDB', turno.id, e.getAttribute("data-empleado"));
                }
        </script>
    </div>
</div>
