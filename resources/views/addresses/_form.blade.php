
@php
    $address = $address ?? null;
    $isEdit = $address !== null;
@endphp

<form
    action="{{ $isEdit ? route('addresses.update', $address->id) : route('addresses.store') }}"
    method="POST"
    id="address-form"
>
    @csrf

    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="address-form-group">
        <label for="label">Label Alamat</label>
        <select name="label" id="label" required>
            @foreach (['Rumah', 'Kantor', 'Lainnya'] as $label)
                <option value="{{ $label }}"
                    {{ old('label', $address->label ?? 'Rumah') === $label ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="address-form-group">
        <label for="recipient_name">Nama Penerima</label>
        <input
            type="text"
            name="recipient_name"
            id="recipient_name"
            value="{{ old('recipient_name', $address->recipient_name ?? '') }}"
            required
            maxlength="255"
        >
    </div>

    <div class="address-form-group">
        <label for="phone">Nomor HP Penerima</label>
        <input
            type="text"
            name="phone"
            id="phone"
            value="{{ old('phone', $address->phone ?? '') }}"
            required
            maxlength="20"
            placeholder="Contoh: 081234567890"
        >
    </div>

    <hr>

    <h3>Wilayah Pengiriman</h3>

    @foreach ([
        'province' => 'Provinsi',
        'city' => 'Kota/Kabupaten',
        'district' => 'Kecamatan',
        'village' => 'Desa/Kelurahan',
    ] as $level => $label)
        <div class="address-form-group">
            <label for="{{ $level }}">{{ $label }}</label>

            <select
                id="{{ $level }}"
                required
                {{ $level !== 'province' ? 'disabled' : '' }}
            >
                <option value="">
                    {{ $level === 'province' ? 'Memuat provinsi...' : 'Pilih wilayah terlebih dahulu' }}
                </option>
            </select>

            <input
                type="hidden"
                name="{{ $level }}_code"
                id="{{ $level }}_code"
                value="{{ old($level . '_code', $address->{$level . '_code'} ?? '') }}"
            >

            <input
                type="hidden"
                name="{{ $level }}_name"
                id="{{ $level }}_name"
                value="{{ old($level . '_name', $address->{$level . '_name'} ?? '') }}"
            >
        </div>
    @endforeach

    <div class="address-form-group">
        <label for="postal_code">Kode Pos</label>
        <input
            type="text"
            name="postal_code"
            id="postal_code"
            value="{{ old('postal_code', $address->postal_code ?? '') }}"
            maxlength="10"
        >
    </div>

    <div class="address-form-group">
        <label for="detail">Detail Alamat</label>
        <textarea
            name="detail"
            id="detail"
            rows="4"
            required
            maxlength="1000"
            placeholder="Nama jalan, nomor rumah, RT/RW, patokan, dan sebagainya"
        >{{ old('detail', $address->detail ?? '') }}</textarea>
    </div>

    <div
        id="region-loading"
        role="status"
        aria-live="polite"
        style="display:none; margin-bottom:15px;"
    >
        Memuat data wilayah...
    </div>

    <div
        id="region-error"
        role="alert"
        style="display:none; color:#c0392b; margin-bottom:15px;"
    ></div>

    <div
        id="region-success"
        role="status"
        style="display:none; color:#16803c; margin-bottom:15px;"
    >
        Data wilayah berhasil dimuat.
    </div>

    <button
        type="submit"
        id="submit-address"
        class="btn btn-primary"
        disabled
    >
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Alamat' }}
    </button>

    <a href="{{ route('profile') }}" class="btn">
        Batal
    </a>
</form>

<style>
    .address-form-group {
        display: grid;
        gap: 8px;
        margin-bottom: 20px;
    }

    .address-form-group label {
        color: var(--c-ink-heading);
        font-size: 14px;
        font-weight: 700;
    }

    .address-form-group input,
    .address-form-group select,
    .address-form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--c-border);
        border-radius: var(--radius-sm);
        background: #fff;
        color: var(--c-ink);
        font: inherit;
        font-size: 14px;
        transition: var(--transition);
    }

    .address-form-group input:focus,
    .address-form-group select:focus,
    .address-form-group textarea:focus {
        outline: none;
        border-color: var(--c-pink-strong);
        box-shadow: 0 0 0 3px rgba(233, 129, 158, 0.15);
    }

    .address-form-group select:disabled {
        opacity: .6;
        cursor: not-allowed;
        background: #F8F4F5;
    }

    #address-form hr {
        border: 0;
        border-top: 1px solid var(--c-border-subtle);
        margin: 28px 0;
    }

    #address-form h3 {
        color: var(--c-ink-heading);
        font-size: 18px;
        margin-bottom: 20px;
    }

    #region-loading, #region-error, #region-success {
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        font-size: 13.5px;
        font-weight: 600;
    }
    #region-loading { background: #FFF9EE; color: #8A5A00; }
    #region-error { background: #FDE7E4; color: #8E2B20; }
    #region-success { background: #E3F6EC; color: #14633F; }

    #submit-address:disabled {
        opacity: .55;
        cursor: not-allowed;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = @json(url('/api/wilayah'));

    const form = document.getElementById('address-form');
    const submitButton = document.getElementById('submit-address');

    const loading = document.getElementById('region-loading');
    const error = document.getElementById('region-error');
    const success = document.getElementById('region-success');

    const levels = ['province', 'city', 'district', 'village'];

    const selects = Object.fromEntries(
        levels.map(level => [
            level,
            document.getElementById(level)
        ])
    );

    const initial = Object.fromEntries(
        levels.map(level => [
            level,
            document.getElementById(level + '_code').value
        ])
    );

    const requestVersion = {
        province: 0,
        city: 0,
        district: 0,
        village: 0
    };

    let initializationFinished = false;
    let activeRequests = 0;

    function setLoading(active) {
        activeRequests = Math.max(0, activeRequests + (active ? 1 : -1));
        loading.style.display = activeRequests > 0 ? 'block' : 'none';
    }

    function showError(message) {
        error.textContent = message;
        error.style.display = 'block';
        success.style.display = 'none';
    }

    function hideMessages() {
        error.style.display = 'none';
        success.style.display = 'none';
    }

    function field(level, suffix) {
        return document.getElementById(level + '_' + suffix);
    }

    function saveRegion(level) {
        const select = selects[level];
        const selected = select.options[select.selectedIndex];

        field(level, 'code').value = select.value;
        field(level, 'name').value =
            select.value && selected ? selected.textContent : '';
    }

    function allRegionsSelected() {
        return levels.every(level => selects[level].value !== '');
    }

    function updateSubmitState() {
        submitButton.disabled =
            !initializationFinished ||
            activeRequests > 0 ||
            !allRegionsSelected();
    }

    function clearBelow(level) {
        const index = levels.indexOf(level);

        levels.slice(index + 1).forEach(child => {
            requestVersion[child]++;

            selects[child].innerHTML =
                '<option value="">Pilih wilayah terlebih dahulu</option>';

            selects[child].disabled = true;

            field(child, 'code').value = '';
            field(child, 'name').value = '';
        });

        updateSubmitState();
    }

    async function loadRegions(level, url, selectedCode = '') {
        const select = selects[level];
        const version = ++requestVersion[level];

        select.disabled = true;
        select.innerHTML =
            '<option value="">Memuat data...</option>';

        setLoading(true);
        hideMessages();
        updateSubmitState();

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(
                    'Gagal mengambil data wilayah. Kode: ' + response.status
                );
            }

            const result = await response.json();

            const regions = Array.isArray(result)
                ? result
                : (Array.isArray(result.data) ? result.data : []);

            if (version !== requestVersion[level]) {
                return false;
            }

            if (regions.length === 0) {
                throw new Error('Data wilayah tidak ditemukan.');
            }

            select.innerHTML =
                '<option value="">Pilih wilayah</option>';

            regions.forEach(region => {
                if (!region.code || !region.name) {
                    return;
                }

                const option = document.createElement('option');
                option.value = String(region.code);
                option.textContent = region.name;

                if (String(region.code) === String(selectedCode)) {
                    option.selected = true;
                }

                select.appendChild(option);
            });

            if (select.options.length <= 1) {
                throw new Error('Format data wilayah tidak sesuai.');
            }

            if (selectedCode && !select.value) {
                throw new Error(
                    'Wilayah tersimpan tidak ditemukan. Silakan pilih ulang.'
                );
            }

            select.disabled = false;

            return true;

        } catch (exception) {
            if (version !== requestVersion[level]) {
                return false;
            }

            select.innerHTML =
                '<option value="">Data gagal dimuat</option>';

            select.disabled = true;

            showError(exception.message);

            console.error('Wilayah error:', exception);

            return false;

        } finally {
            setLoading(false);
            updateSubmitState();
        }
    }

    async function initializeRegions() {
        initializationFinished = false;
        updateSubmitState();

        const provinceLoaded = await loadRegions(
            'province',
            baseUrl + '/provinces',
            initial.province
        );

        if (!provinceLoaded) {
            return;
        }

        if (initial.province) {
            saveRegion('province');

            const cityLoaded = await loadRegions(
                'city',
                baseUrl + '/regencies/' + encodeURIComponent(initial.province),
                initial.city
            );

            if (!cityLoaded) {
                return;
            }
        }

        if (initial.city) {
            saveRegion('city');

            const districtLoaded = await loadRegions(
                'district',
                baseUrl + '/districts/' + encodeURIComponent(initial.city),
                initial.district
            );

            if (!districtLoaded) {
                return;
            }
        }

        if (initial.district) {
            saveRegion('district');

            const villageLoaded = await loadRegions(
                'village',
                baseUrl + '/villages/' + encodeURIComponent(initial.district),
                initial.village
            );

            if (!villageLoaded) {
                return;
            }
        }

        if (initial.village) {
            saveRegion('village');
        }

        initializationFinished = true;

        success.style.display = 'block';
        updateSubmitState();
    }

    selects.province.addEventListener('change', async function () {
        initializationFinished = false;

        saveRegion('province');
        clearBelow('province');

        if (!this.value) {
            initializationFinished = true;
            updateSubmitState();
            return;
        }

        const loaded = await loadRegions(
            'city',
            baseUrl + '/regencies/' + encodeURIComponent(this.value)
        );

        initializationFinished = loaded;
        updateSubmitState();
    });

    selects.city.addEventListener('change', async function () {
        initializationFinished = false;

        saveRegion('city');
        clearBelow('city');

        if (!this.value) {
            initializationFinished = true;
            updateSubmitState();
            return;
        }

        const loaded = await loadRegions(
            'district',
            baseUrl + '/districts/' + encodeURIComponent(this.value)
        );

        initializationFinished = loaded;
        updateSubmitState();
    });

    selects.district.addEventListener('change', async function () {
        initializationFinished = false;

        saveRegion('district');
        clearBelow('district');

        if (!this.value) {
            initializationFinished = true;
            updateSubmitState();
            return;
        }

        const loaded = await loadRegions(
            'village',
            baseUrl + '/villages/' + encodeURIComponent(this.value)
        );

        initializationFinished = loaded;
        updateSubmitState();
    });

    selects.village.addEventListener('change', function () {
        saveRegion('village');
        updateSubmitState();
    });

    form.addEventListener('submit', function (event) {
        levels.forEach(saveRegion);

        if (!initializationFinished || !allRegionsSelected()) {
            event.preventDefault();

            showError(
                'Pastikan semua wilayah sudah dipilih dan data selesai dimuat.'
            );

            return;
        }
    });

    initializeRegions();
});
</script>
