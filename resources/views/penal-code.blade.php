@extends('layouts.app')

@section('title', 'UNDANG UNDANG KEPOLISIAN DUNIA KITA')

@section('content')
    @include('partials.header')

    <main class="container mx-auto flex flex-1 flex-col p-4 md:p-6">
        <div class="grid flex-1 grid-cols-1 gap-6 md:grid-cols-3">
            <section class="panel overflow-y-auto rounded-2xl p-5 shadow-md transition duration-300 hover:shadow-lg">
                <div id="hasil" class="mb-3 rounded-lg border p-3 text-sm leading-relaxed" aria-live="polite">
                    Belum ada pasal dipilih.
                </div>

                <div class="mb-3 rounded-lg border border-purple-400/30 bg-purple-900/20 p-3 transition hover:bg-purple-900/25">
                    <label class="inline-flex cursor-pointer items-center gap-2">
                        <input type="checkbox" id="dpoCheck">
                        <span class="flex items-center gap-1 font-medium text-yellow-300">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Tersangka DPO</span>
                        </span>
                    </label>

                    <p class="mt-1 text-center text-xs text-yellow-300">
                        Jika DPO, total kurungan penjara dan denda akan dikalikan {{ $settings['dpoMultiplier'] }} (x{{ $settings['dpoMultiplier'] }}).
                    </p>
                </div>

                <button type="button" id="btnSalin" class="btn-salin mb-2 flex w-full items-center justify-center gap-2 rounded-lg py-2 font-semibold shadow-md transition hover:scale-[1.03]">
                    <i class="bi bi-clipboard-check-fill"></i><span>Salin Pasal</span>
                </button>

                <button type="button" id="btnReset" class="btn-reset flex w-full items-center justify-center gap-2 rounded-lg py-2 font-semibold shadow-md transition hover:scale-[1.03]">
                    <i class="bi bi-arrow-counterclockwise"></i><span>Reset</span>
                </button>

                <div class="rules mt-3 rounded-xl border p-3 text-sm text-gray-700">
                    <p class="mb-1 font-semibold">PERATURAN:</p>
                    <ol class="list-decimal space-y-1 pl-5">
                        <li><b>Maksimal hanya memilih {{ $settings['maxPasal'] }} pasal</b> undang-undang.</li>
                        <li><b>Denda mengambil dari yang tertinggi Masa tahanan di akumulasikan.</b> Sesuai ketentuan yang berlaku.</li>
                    </ol>

                    <div class="mt-2 text-xs text-gray-500">
                        <p class="mb-1 font-semibold">Catatan:</p>
                        <ul class="list-disc space-y-1 pl-5">
                            <li>Gunakan fitur <b>Search</b> untuk mencari pasal lebih cepat (kode / nama).</li>
                            <li>Klik tombol <b>Deskripsi</b> untuk melihat detail pasal sebelum memilih.</li>
                            <li>Reset akan menghapus semua pilihan dan mengembalikan perhitungan ke nol.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="panel flex flex-col rounded-2xl p-5 shadow-md transition duration-300 hover:shadow-lg md:col-span-2">
                <div class="mb-4 flex flex-wrap gap-2" role="tablist">
                    <button type="button" id="tabUmum" data-tab="Umum" class="tab-btn" role="tab">
                        <i class="bi bi-people-fill"></i><span>Umum</span>
                    </button>
                    <button type="button" id="tabLalin" data-tab="Lalin" class="tab-btn" role="tab">
                        <i class="bi bi-signpost-2-fill"></i><span>Lalu Lintas</span>
                    </button>
                    <button type="button" id="tabKendaraan" data-tab="Kendaraan" class="tab-btn" role="tab">
                        <i class="bi bi-car-front-fill"></i><span>Kendaraan</span>
                    </button>
                </div>

                <div class="relative mb-4">
                    <i class="bi bi-search pointer-events-none absolute top-2.5 left-3 text-gray-400"></i>
                    <input type="search" id="search" placeholder="Search" autocomplete="off" class="w-full rounded-lg border-2 p-2 pl-10 focus:outline-none">
                </div>

                <div id="list-pasal" class="max-h-[70vh] flex-1 space-y-2 overflow-y-auto pr-1 md:max-h-none"></div>
            </section>
        </div>
    </main>

    <div id="toast" class="fixed top-[84px] left-1/2 z-[9999] hidden -translate-x-1/2 items-center gap-2 rounded-lg px-4 py-2 shadow-md" role="status">
        <i class="bi bi-check-circle-fill"></i>
        <span id="toastText"></span>
    </div>

    @include('partials.description-modal')
    @include('partials.footer')

    <script type="application/json" id="penal-data">{!! $bootstrap !!}</script>
@endsection

@push('scripts')
    <script src="{{ \App\Support\Asset::url('js/app.js') }}" defer></script>
@endpush
