@php
    $logoUrl = $setting->logo_path ? \Illuminate\Support\Facades\Storage::url($setting->logo_path) : null;
    $faviconUrl = $setting->favicon_path ? \Illuminate\Support\Facades\Storage::url($setting->favicon_path) : null;
@endphp

<form action="{{ route('admin.school-settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-info-circle-fill me-2"></i> Identitas Sekolah</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                            <input type="text" name="school_name" class="form-control @error('school_name') is-invalid @enderror" value="{{ old('school_name', $setting->school_name) }}" required>
                            @error('school_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama Singkat <span class="text-danger">*</span></label>
                            <input type="text" name="school_short_name" class="form-control @error('school_short_name') is-invalid @enderror" value="{{ old('school_short_name', $setting->school_short_name) }}" required>
                            @error('school_short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $setting->address) }}" required>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Desa/Kelurahan</label>
                            <input type="text" name="village" class="form-control @error('village') is-invalid @enderror" value="{{ old('village', $setting->village) }}">
                            @error('village') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="district" class="form-control @error('district') is-invalid @enderror" value="{{ old('district', $setting->district) }}">
                            @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kota/Kabupaten</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $setting->city) }}">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Provinsi</label>
                            <input type="text" name="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province', $setting->province) }}">
                            @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $setting->postal_code) }}">
                            @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-person-badge-fill me-2"></i> Legal & Kepala Sekolah</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NPSN</label>
                            <input type="text" name="npsn" class="form-control @error('npsn') is-invalid @enderror" value="{{ old('npsn', $setting->npsn) }}">
                            @error('npsn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NSM</label>
                            <input type="text" name="nsm" class="form-control @error('nsm') is-invalid @enderror" value="{{ old('nsm', $setting->nsm) }}">
                            @error('nsm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Akreditasi</label>
                            <input type="text" name="accreditation" class="form-control @error('accreditation') is-invalid @enderror" value="{{ old('accreditation', $setting->accreditation) }}">
                            @error('accreditation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Sekolah</label>
                            <input type="text" name="school_status" class="form-control @error('school_status') is-invalid @enderror" value="{{ old('school_status', $setting->school_status) }}">
                            @error('school_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kepala Sekolah <span class="text-danger">*</span></label>
                            <input type="text" name="principal_name" class="form-control @error('principal_name') is-invalid @enderror" value="{{ old('principal_name', $setting->principal_name) }}" required>
                            @error('principal_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">NIP Kepala Sekolah</label>
                            <input type="text" name="principal_nip" class="form-control @error('principal_nip') is-invalid @enderror" value="{{ old('principal_nip', $setting->principal_nip) }}">
                            @error('principal_nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">NUPTK Kepala Sekolah</label>
                            <input type="text" name="principal_nuptk" class="form-control @error('principal_nuptk') is-invalid @enderror" value="{{ old('principal_nuptk', $setting->principal_nuptk) }}">
                            @error('principal_nuptk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Nama Yayasan</label>
                            <input type="text" name="foundation_name" class="form-control @error('foundation_name') is-invalid @enderror" value="{{ old('foundation_name', $setting->foundation_name) }}">
                            @error('foundation_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-flag-fill me-2"></i> Profil Singkat</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Visi</label>
                        <textarea name="vision" rows="3" class="form-control @error('vision') is-invalid @enderror">{{ old('vision', $setting->vision) }}</textarea>
                        @error('vision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Misi</label>
                        <textarea name="mission" rows="4" class="form-control @error('mission') is-invalid @enderror">{{ old('mission', $setting->mission) }}</textarea>
                        @error('mission') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Motto</label>
                        <input type="text" name="motto" class="form-control @error('motto') is-invalid @enderror" value="{{ old('motto', $setting->motto) }}">
                        @error('motto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-image-fill me-2"></i> Logo & Favicon</div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Logo</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="border rounded d-flex align-items-center justify-content-center bg-light" style="width:80px;height:80px;">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="Logo sekolah" width="70" height="70" loading="lazy" decoding="async" style="max-width:70px;max-height:70px;object-fit:contain;">
                                @else
                                    <i class="bi bi-buildings text-muted fs-2"></i>
                                @endif
                            </div>
                            <div class="small text-muted">JPG, PNG, atau WEBP. Maksimal 2MB.</div>
                        </div>
                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Favicon</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="border rounded d-flex align-items-center justify-content-center bg-light" style="width:48px;height:48px;">
                                @if($faviconUrl)
                                    <img src="{{ $faviconUrl }}" alt="Favicon sekolah" width="32" height="32" loading="lazy" decoding="async" style="max-width:32px;max-height:32px;object-fit:contain;">
                                @else
                                    <i class="bi bi-bookmark-star text-muted fs-4"></i>
                                @endif
                            </div>
                            <div class="small text-muted">ICO, PNG, JPG, atau WEBP. Maksimal 1MB.</div>
                        </div>
                        <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror" accept=".ico,.png,.jpg,.jpeg,.webp">
                        @error('favicon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-telephone-fill me-2"></i> Kontak</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $setting->phone) }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp', $setting->whatsapp) }}">
                        @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $setting->email) }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $setting->website) }}" placeholder="https://example.sch.id">
                        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-calendar-check-fill me-2"></i> Akademik Sekolah</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-7 mb-3">
                            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="school_year" class="form-control @error('school_year') is-invalid @enderror" value="{{ old('school_year', $setting->school_year) }}" required>
                            @error('school_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-5 mb-3">
                            <label class="form-label">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                                @php
                                    $semesterValue = old('semester', $setting->semester);
                                @endphp
                                <option value="Ganjil" {{ $semesterValue === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ $semesterValue === 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                            @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 mb-4">
                <i class="bi bi-save me-1"></i> Simpan Pengaturan Sekolah
            </button>
        </div>
    </div>
</form>
