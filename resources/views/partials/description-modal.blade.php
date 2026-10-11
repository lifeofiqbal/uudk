<div id="descModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="descTitle">
    <div id="descOverlay" class="absolute inset-0 bg-black/40"></div>

    <div class="modal-panel relative mx-auto mt-24 w-[92%] max-w-md rounded-2xl border p-4 shadow-xl">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 id="descTitle" class="text-base font-semibold"></h3>
                <p id="descCode" class="mt-0.5 text-xs text-gray-500"></p>
            </div>

            <button type="button" id="descClose" class="shrink-0 rounded-lg px-2 py-1 text-purple-400 transition hover:bg-white/10" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="mt-3 text-sm leading-relaxed text-gray-700">
            <p id="descText"></p>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="button" id="descClose2" class="rounded-lg bg-white/10 px-3 py-2 text-sm font-semibold text-purple-300 transition hover:bg-white/15">
                Tutup
            </button>
        </div>
    </div>
</div>
