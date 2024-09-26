<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="profile-title">Tambah Alamat</h3>
        <button class="btn btn-info rounded-box shadow-sm" onclick="kembaliKealamat()" title="Kembali ke Menu Alamat">
            <i class="material-icons">arrow_back</i>
        </button>
    </div>
    <form id="formTambahAlamat">
        <div class="form-group">
            <label for="nama_penerima">Nama Penerima</label>
            <input type="text" class="form-control" id="nama_penerima" value="{{ $address->address_name ?? '' }}" required>
            <input type="hidden" id="id" value="{{ $address->member_address_id ?? null }}">
        </div>
        <div class="form-group">
            <label for="no_telepon">Nomor Telepon</label>
            <input type="text" class="form-control" id="no_telepon" value="{{ $address->phone ?? '' }}" required>
        </div>
        <div class="form-group">
            <label for="provinsi">Provinsi</label>
            <select class="form-control" id="provinsi" required>
                <option value="">Pilih Provinsi</option>
                @foreach ($provinces as $province)
                @if ($address != 'empty')
                <option value="{{ $province->province_id }}"
                    @if(isset($address) && $address->province_id == $province->province_id) selected @endif>
                    {{ $province->province_name }}
                </option>
                @else
                <option value="{{ $province->province_id }}">
                    {{ $province->province_name }}
                </option>
                @endif

                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="kota">Kota</label>
            <select class="form-control" id="kota" required>
                <option value="">Pilih Kota</option>
                <!-- Data kota akan diisi melalui AJAX -->
            </select>
        </div>
        <div class="form-group">
            <label for="kecamatan">Kecamatan</label>
            <select class="form-control" id="kecamatan" required>
                <option value="">Pilih Kecamatan</option>
                <!-- Data kecamatan akan diisi melalui AJAX -->
            </select>
        </div>
        <div class="form-group">
            <label for="kode_pos">Kode Pos</label>
            <input type="text" class="form-control" id="kode_pos" value="{{ $address->postal_code ?? '' }}" required>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat Lengkap</label>
            <textarea class="form-control" id="alamat" rows="3" required>
            {{ $address->address ?? '' }}
            </textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Alamat</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Jika ada alamat yang sudah ada, ambil data kota dan kecamatan
        var address = '{!! json_encode($address) !!}'; // Hapus tanda kutip tambahan
        console.log(address);
        if (address != 'empty') {
            ajaxCity('{{ $address->province_id ?? null }}', function() {
                $('#kota').val('{{ $address->city_id ?? null }}');
            });
            ajaxdistrix('{{ $address->city_id ?? null }}', function() {
                $('#kecamatan').val('{{ $address->subdistrict_id ?? null }}');
            });
        }

        // Menangani perubahan pada dropdown provinsi
        $('#provinsi').change(function() {
            var provinsiId = $(this).val();
            $('#kota').empty().append(new Option('Pilih Kota', ''));
            $('#kecamatan').empty().append(new Option('Pilih Kecamatan', ''));
            ajaxCity(provinsiId);
        });

        function ajaxCity(id_province, callback) { // Tambahkan parameter callback
            if (!id_province) {
                return;
            }

            $.ajax({
                url: appUrl + '/api/config/getCity/' + id_province,
                method: 'GET',
                success: function(data) {
                    $('#kota').empty().append(new Option('Pilih Kota', '')); // Kosongkan dropdown sebelum diisi
                    $.each(data.citys, function(index, kota) {
                        $('#kota').append(new Option(kota.city_name, kota.city_id));
                    });
                    if (callback) callback(); // Panggil callback jika ada
                },
                error: function(xhr) {
                    console.error('Error fetching cities:', xhr);
                }
            });
        }

        // Menangani perubahan pada dropdown kota
        $('#kota').change(function() {
            var kotaId = $(this).val();
            $('#kecamatan').empty().append(new Option('Pilih Kecamatan', ''));
            ajaxdistrix(kotaId);
        });

        function ajaxdistrix(id_city, callback) { // Tambahkan parameter callback
            if (!id_city) {
                return;
            }

            $.ajax({
                url: appUrl + '/api/config/getdistrict/' + id_city,
                method: 'GET',
                success: function(data) {
                    $('#kecamatan').empty().append(new Option('Pilih Kecamatan', '')); // Kosongkan dropdown sebelum diisi
                    $.each(data.subdistricts, function(index, kecamatan) {
                        $('#kecamatan').append(new Option(kecamatan.subdistrict_name, kecamatan.subdistrict_id));
                    });
                    if (callback) callback(); // Panggil callback jika ada
                },
                error: function(xhr) {
                    console.error('Error fetching districts:', xhr);
                }
            });
        }

        // Menangani pengiriman form
        $('#formTambahAlamat').submit(function(e) {
            e.preventDefault();

            // Mengambil nilai dari setiap input
            var id = $('#id').val();
            var namaPenerima = $('#nama_penerima').val();
            var noTelepon = $('#no_telepon').val();
            var provinsi = $('#provinsi').val();
            var kota = $('#kota').val();
            var kecamatan = $('#kecamatan').val();
            var kodePos = $('#kode_pos').val();
            var alamat = $('#alamat').val();

            // Menyusun data yang akan dikirim
            var formData = {
                id: id,
                nama_penerima: namaPenerima,
                no_telepon: noTelepon,
                provinsi: provinsi,
                kota: kota,
                kecamatan: kecamatan,
                kode_pos: kodePos,
                alamat: alamat
            };

            // AJAX untuk mengirim data
            $.ajax({
                url: appUrl + '/api/member/storeAddress', // Ganti dengan URL API Anda
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire('Berhasil!', 'Alamat berhasil ditambahkan!', 'success');
                    $('#formTambahAlamat')[0].reset(); // Reset form
                    kembaliKealamat();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menambahkan alamat.');
                }
            });
        });
    });

    function kembaliKealamat() {
        var url = "{{ route('profile.address') }}";
        loadContent(url, $('#contentArea'));
    }
</script>