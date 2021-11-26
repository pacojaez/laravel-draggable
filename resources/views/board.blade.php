<div>

    <div class="w-full h-full flex space-x-4 overflow-x-auto">
        @foreach($statuses as $status)
            <div class="h-full flex-1">
                <div class="bg-blue-200 rounded px-2 flex flex-col h-full" id="{{ $status['id'] }}">

                    <div class="p-2 text-sm text-gray-700">
                        {{ $status['title'] }}
                    </div>

                    <div
                        id="{{ $status['statusRecordsId'] }}"
                        data-status-id="{{ $status['id'] }}"
                        class="space-y-2 p-2 flex-1 overflow-y-auto">

                        @foreach($status['records'] as $record)
                            <div
                                id="{{ $record['id'] }}"
                                @if($recordClickEnabled)
                                wire:click="onRecordClick('{{ $record['id'] }}')"
                                @endif
                                class="shadow bg-white p-2 rounded border">

                                <p>
                                    {{ $record['title'] }}
                                </p>

                            </div>
                        @endforeach

                    </div>

                    <div class="">

                    </div>

                </div>
            </div>
        @endforeach
    </div>


    <div wire:ignore>
        <script>
            window.onload = () => {
                @foreach($statuses as $status)
                Sortable.create(document.getElementById('{{ $status['statusRecordsId'] }}'), {
                    group: '{{ $status['group'] }}',
                    animation: 0,
                    ghostClass: 'bg-indigo-100',
                    swapThreshold: 0,
                    sort: false,


                    setData: function (dataTransfer, dragEl) {
                        dataTransfer.setData('id', dragEl.id);
                    },

                    onEnd: function (evt) {
                        const sameContainer = evt.from === evt.to;
                        const orderChanged = evt.oldIndex !== evt.newIndex;

                        if (sameContainer && !orderChanged) {
                            return;
                        }

                        const recordId = evt.item.id;

                        const fromStatusId = evt.from.dataset.statusId;
                        const fromOrderedIds = [].slice.call(evt.from.children).map(child => child.id);

                        if (sameContainer) {
                        @this.call('onStatusSorted', recordId, fromStatusId, fromOrderedIds);
                            return;
                        }

                        const toStatusId = evt.to.dataset.statusId;
                        const toOrderedIds = [].slice.call(evt.to.children).map(child => child.id);

                    @this.call('onStatusChanged', recordId, toStatusId, fromOrderedIds, toOrderedIds);
                    },
                });
                @endforeach
            }
        </script>
    </div>
</div>
