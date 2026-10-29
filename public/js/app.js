(() => {
  "use strict";

  // const TABS = ["Umum", "Lalin", "Kendaraan"];
  const TABS = ["Umum", "Kendaraan"];

  const state = {
    kategoriUmumOrder: [],
    // pasal: { Umum: [], Lalin: [], Kendaraan: [] },
    pasal: { Umum: [], Kendaraan: [] },
    currentTab: "Umum",
    selectedCodes: new Set(),
    index: new Map(),
    maxPasal: 7,
    dpoMultiplier: 2,
    toastTimer: null,
  };

  const dom = {};

  function cacheDom() {
    const ids = [
      "list-pasal", "search", "tabUmum", /* "tabLalin", */ "tabKendaraan", "hasil",
      "dpoCheck", "btnSalin", "btnReset", "toast", "toastText", "descModal",
      "descOverlay", "descClose", "descClose2", "descTitle", "descCode", "descText",
    ];
    ids.forEach((id) => (dom[id] = document.getElementById(id)));
    dom.list = dom["list-pasal"];
    // dom.tabs = { Umum: dom.tabUmum, Lalin: dom.tabLalin, Kendaraan: dom.tabKendaraan };
    dom.tabs = { Umum: dom.tabUmum, Kendaraan: dom.tabKendaraan };
  }

  function asArray(value) {
    return Array.isArray(value) ? value : [];
  }

  function loadData() {
    const el = document.getElementById("penal-data");
    if (!el) throw new Error("Data pasal tidak ditemukan");

    const parsed = JSON.parse(el.textContent || "{}");
    const data = parsed.data || {};
    const settings = parsed.settings || {};

    state.maxPasal = Number(settings.maxPasal) > 0 ? Number(settings.maxPasal) : 7;
    state.dpoMultiplier = Number(settings.dpoMultiplier) > 0 ? Number(settings.dpoMultiplier) : 2;
    state.kategoriUmumOrder = asArray(data.kategoriUmumOrder);
    state.pasal.Umum = asArray(data.pasalUmum);
    // state.pasal.Lalin = asArray(data.pasalLalin);
    state.pasal.Kendaraan = asArray(data.pasalKendaraan);

    state.index.clear();
    TABS.forEach((source) => {
      state.pasal[source].forEach((pasal) => {
        state.index.set(String(pasal.kode), { pasal, source });
      });
    });
  }

  function normalize(value) {
    return String(value ?? "").toLowerCase().trim();
  }

  function escapeHtml(value) {
    return String(value ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#39;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;");
  }

  function toNumber(value) {
    const n = Number(value);
    return Number.isFinite(n) ? n : 0;
  }

  function formatMoney(value) {
    return "$" + toNumber(value).toLocaleString("id-ID");
  }

  function durationLabel(pasal, source) {
      if (source === "Kendaraan") return `${toNumber(pasal.jam)} jam`;
    return `${toNumber(pasal.bulan)} bulan`;
  }

  function showToast(message) {
    if (!dom.toast) return;
    dom.toastText.textContent = message;
    dom.toast.classList.remove("hidden");
    dom.toast.classList.add("flex");
    clearTimeout(state.toastTimer);
    state.toastTimer = setTimeout(() => {
      dom.toast.classList.add("hidden");
      dom.toast.classList.remove("flex");
    }, 2000);
  }

  function openDescModal(kode) {
    const found = state.index.get(String(kode));
    if (!found || !dom.descModal) return;
    const { pasal } = found;
    dom.descTitle.textContent = pasal.nama || "Deskripsi Pasal";
    dom.descCode.textContent = pasal.kode ? `Kode: ${pasal.kode}` : "";
    dom.descText.textContent = pasal.deskripsi || "Deskripsi belum tersedia.";
    dom.descModal.classList.remove("hidden");
    document.body.classList.add("overflow-hidden");
    dom.descClose?.focus();
  }

  function closeDescModal() {
    if (!dom.descModal || dom.descModal.classList.contains("hidden")) return;
    dom.descModal.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");
  }

  function highlightTab() {
    TABS.forEach((tab) => {
      const el = dom.tabs[tab];
      if (!el) return;
      const active = state.currentTab === tab;
      el.classList.toggle("is-active", active);
      el.setAttribute("aria-selected", active ? "true" : "false");
    });
  }

  function createCard(pasal, source) {
    const card = document.createElement("div");
    card.className = "pasal-card flex items-center justify-between gap-3 rounded-xl p-3 transition";

    const kode = String(pasal.kode);
    const checked = state.selectedCodes.has(kode) ? "checked" : "";

    card.innerHTML = `
      <label class="flex flex-1 cursor-pointer items-start gap-3">
        <input class="pasal-check mt-0.5 shrink-0" type="checkbox" value="${escapeHtml(kode)}" ${checked}>
        <span class="text-gray-200">
          <span class="font-bold text-yellow-300">${escapeHtml(kode)}</span>
          <span class="ml-1">${escapeHtml(pasal.nama)}</span>
        </span>
      </label>
      <div class="flex shrink-0 flex-col items-end gap-1 text-right text-xs text-gray-300">
        <div><i class="bi bi-cash-stack text-yellow-300"></i> ${escapeHtml(formatMoney(pasal.denda))}</div>
        <div><i class="bi bi-calendar-event text-purple-300"></i> ${escapeHtml(durationLabel(pasal, source))}</div>
        <button type="button" class="btn-desc mt-1 inline-flex items-center gap-1 rounded-lg border border-yellow-300/20 bg-yellow-300/15 px-2 py-1 text-yellow-200 transition hover:bg-yellow-300/20" data-kode="${escapeHtml(kode)}" title="Lihat deskripsi">
          <i class="bi bi-info-circle"></i>
          <span class="text-[11px] font-semibold">Deskripsi</span>
        </button>
      </div>
    `;

    return card;
  }

  function renderList() {
    const keyword = normalize(dom.search?.value);
    const source = state.currentTab;
    const data = state.pasal[source].filter((p) => {
      if (!keyword) return true;
      return normalize(p.nama).includes(keyword) || normalize(p.kode).includes(keyword);
    });

    const frag = document.createDocumentFragment();

    if (!data.length) {
      const empty = document.createElement("p");
      empty.className = "py-6 text-center text-sm text-gray-400";
      empty.textContent = "Pasal tidak ditemukan.";
      frag.appendChild(empty);
    } else if (source === "Umum") {
      const groups = new Map();
      state.kategoriUmumOrder.forEach((k) => groups.set(k, []));

      data.forEach((p) => {
        const kategori = p.kategori || "KETERTIBAN UMUM";
        if (!groups.has(kategori)) groups.set(kategori, []);
        groups.get(kategori).push(p);
      });

      groups.forEach((items, kategori) => {
        if (!items.length) return;

        const details = document.createElement("details");
        details.className = "mb-2 rounded-lg border border-white/10 bg-black/15";
        details.open = true;

        const summary = document.createElement("summary");
        summary.className = "cursor-pointer p-2 text-lg font-bold text-yellow-300";
        summary.textContent = kategori;
        details.appendChild(summary);

        const wrap = document.createElement("div");
        wrap.className = "space-y-2 px-2 pb-2";
        items.forEach((p) => wrap.appendChild(createCard(p, "Umum")));
        details.appendChild(wrap);

        frag.appendChild(details);
      });
    } else {
      data.forEach((p) => frag.appendChild(createCard(p, source)));
    }

    dom.list.replaceChildren(frag);
    updateSummary();
  }

  function calculate() {
    const multiplier = dom.dpoCheck?.checked ? state.dpoMultiplier : 1;
    const items = [];
    let denda = 0;
    let bulan = 0;
    let jam = 0;

    state.selectedCodes.forEach((kode) => {
      const found = state.index.get(kode);
      if (!found) return;
      const { pasal, source } = found;
      items.push({ pasal, source });
      denda += toNumber(pasal.denda);
      if (source === "Kendaraan") jam += toNumber(pasal.jam);
      else bulan += toNumber(pasal.bulan);
    });

    return {
      items,
      denda: denda * multiplier,
      bulan: bulan * multiplier,
      jam: jam * multiplier,
    };
  }

  function updateSummary() {
    const result = calculate();

    const list = result.items.length
      ? result.items
          .map(({ pasal, source }) =>
            `<li class="mb-1">${escapeHtml(pasal.kode)} - ${escapeHtml(pasal.nama)} (Denda: ${escapeHtml(formatMoney(pasal.denda))}, Durasi: ${escapeHtml(durationLabel(pasal, source))})</li>`
          )
          .join("")
      : "<li>Tidak ada pasal dipilih</li>";

    // <p><strong>Total Masa Tahanan (Umum &amp; Lalu Lintas):</strong> ${result.bulan} bulan</p>
    dom.hasil.innerHTML = `
      <h5 class="font-semibold text-yellow-300">Pasal Terpilih (${result.items.length}/${state.maxPasal}):</h5>
      <ul class="mb-2 list-disc pl-5">${list}</ul>
      <p><strong>Total Denda:</strong> ${escapeHtml(formatMoney(result.denda))}</p>
      <p><strong>Total Masa Tahanan (Umum):</strong> ${result.bulan} bulan</p>
      <p><strong>Total Masa Tahanan (Kendaraan):</strong> ${result.jam} jam</p>
    `;
  }

  function onCheckChange(input) {
    const kode = String(input.value);

    if (input.checked) {
      if (state.selectedCodes.size >= state.maxPasal) {
        input.checked = false;
        showToast(`Maksimal hanya boleh memilih ${state.maxPasal} pasal.`);
        return;
      }
      state.selectedCodes.add(kode);
    } else {
      state.selectedCodes.delete(kode);
    }

    updateSummary();
  }

  async function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(text);
      return;
    }

    const area = document.createElement("textarea");
    area.value = text;
    area.setAttribute("readonly", "");
    area.style.position = "fixed";
    area.style.opacity = "0";
    document.body.appendChild(area);
    area.select();
    const ok = document.execCommand("copy");
    area.remove();
    if (!ok) throw new Error("copy failed");
  }

  async function salinPasal() {
    const text = Array.from(state.selectedCodes)
      .map((kode) => state.index.get(kode)?.pasal?.nama)
      .filter(Boolean)
      .join(", ");

    if (!text) {
      showToast("Belum ada pasal dipilih.");
      return;
    }

    try {
      await copyText(text);
      showToast("Pasal berhasil disalin!");
    } catch {
      showToast("Gagal menyalin (browser tidak mengizinkan).");
    }
  }

  function resetForm() {
    state.selectedCodes.clear();
    if (dom.dpoCheck) dom.dpoCheck.checked = false;
    renderList();
    showToast("Pilihan pasal telah direset.");
  }

  function debounce(fn, wait) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), wait);
    };
  }

  function bindEvents() {
    dom.search?.addEventListener("input", debounce(renderList, 120));

    TABS.forEach((tab) => {
      dom.tabs[tab]?.addEventListener("click", () => {
        if (state.currentTab === tab) return;
        state.currentTab = tab;
        highlightTab();
        renderList();
      });
    });

    dom.list?.addEventListener("change", (e) => {
      if (e.target.matches(".pasal-check")) onCheckChange(e.target);
    });

    dom.list?.addEventListener("click", (e) => {
      const btn = e.target.closest(".btn-desc");
      if (btn) openDescModal(btn.dataset.kode);
    });

    dom.dpoCheck?.addEventListener("change", updateSummary);
    dom.btnSalin?.addEventListener("click", salinPasal);
    dom.btnReset?.addEventListener("click", resetForm);

    [dom.descOverlay, dom.descClose, dom.descClose2].forEach((el) =>
      el?.addEventListener("click", closeDescModal)
    );

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeDescModal();
    });
  }

  function init() {
    cacheDom();
    highlightTab();
    bindEvents();

    try {
      loadData();
      renderList();
    } catch (err) {
      console.error(err);
      if (dom.hasil) {
        dom.hasil.innerHTML = '<p class="font-semibold text-red-400">Gagal memuat data pasal.</p>';
      }
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();