<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat - {{ $certificate->student->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #fdfaf3;
        }

        .outer-border {
            border: 3px solid #92722a;
            padding: 10px;
            margin: 10px;
        }
        .inner-border {
            border: 1.5px solid #92722a;
            padding: 40px 60px;
            text-align: center;
            min-height: 480px;
            position: relative;
        }

        .corner {
            position: absolute;
            width: 36px;
            height: 36px;
            border: 2px solid #b8912f;
        }
        .corner-tl { top: -1.5px; left: -1.5px; border-right: none; border-bottom: none; }
        .corner-tr { top: -1.5px; right: -1.5px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: -1.5px; left: -1.5px; border-right: none; border-top: none; }
        .corner-br { bottom: -1.5px; right: -1.5px; border-left: none; border-top: none; }

        .eyebrow {
            font-size: 11px;
            letter-spacing: 4px;
            color: #92722a;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .school-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }

        .title {
            font-size: 34px;
            font-weight: 700;
            color: #1e3a8a;
            letter-spacing: 3px;
            margin-top: 26px;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            letter-spacing: 1px;
        }

        .given-to {
            font-size: 12px;
            color: #64748b;
            margin-top: 30px;
            letter-spacing: 1px;
        }
        .student-name {
            font-size: 30px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 8px;
            padding-bottom: 10px;
            border-bottom: 1.5px solid #b8912f;
            display: inline-block;
            min-width: 420px;
        }
        .student-meta {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 8px;
        }

        .citation {
            font-size: 13px;
            color: #334155;
            margin: 26px auto 0;
            max-width: 560px;
            line-height: 1.7;
        }
        .citation .award-title {
            font-weight: 700;
            color: #1e3a8a;
            font-size: 15px;
            display: block;
            margin-bottom: 6px;
        }
        .points-badge {
            display: inline-block;
            margin-top: 14px;
            padding: 6px 18px;
            border: 1px solid #b8912f;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            color: #92722a;
            letter-spacing: 0.5px;
        }

        .signatures {
            width: 100%;
            margin-top: 50px;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            font-size: 10px;
            color: #475569;
            vertical-align: bottom;
        }
        .sign-space { height: 50px; }
        .sign-space img { max-height: 48px; max-width: 160px; }
        .sign-line {
            border-top: 1px solid #334155;
            width: 200px;
            margin: 0 auto 4px;
            padding-top: 4px;
            font-weight: 700;
            color: #0f172a;
        }
        .sign-role {
            font-weight: 400;
            color: #64748b;
            font-size: 9.5px;
        }

        .footer-note {
            margin-top: 28px;
            font-size: 8.5px;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }
        .cert-number {
            position: absolute;
            bottom: 12px;
            right: 16px;
            font-size: 8.5px;
            color: #94a3b8;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="outer-border">
        <div class="inner-border">
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>

            <div class="eyebrow">Sekolah</div>
            <div class="school-name">{{ $certificate->school->name ?? website_name() }}</div>

            <div class="title">Sertifikat Penghargaan</div>
            <div class="subtitle">Certificate of Appreciation</div>

            <div class="given-to">Dengan bangga diberikan kepada</div>
            <div class="student-name">{{ $certificate->student->name }}</div>
            <div class="student-meta">
                NISN {{ $certificate->student->nisn }}
                @if($certificate->student->currentClass)
                    &nbsp;&middot;&nbsp; Kelas {{ $certificate->student->currentClass->name }}
                @endif
            </div>

            <div class="citation">
                <span class="award-title">{{ $certificate->ruleThreshold->action ?? 'Penghargaan Prestasi' }}</span>
                {{ $certificate->ruleThreshold->description ?? 'Atas prestasi dan dedikasi yang ditunjukkan selama masa pendidikan.' }}
                <br>
                <span class="points-badge">{{ $certificate->points_at_issue }} Poin Prestasi</span>
            </div>

            @php
                $school = $certificate->school;
                $embedSignature = function (?string $path) {
                    if (! $path) {
                        return null;
                    }
                    $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
                    if (! is_file($fullPath)) {
                        return null;
                    }
                    $mime = mime_content_type($fullPath) ?: 'image/png';

                    return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($fullPath));
                };
                $principalSignature = $embedSignature($school?->principalSignaturePath());
                $kesiswaanSignature = $embedSignature($school?->kesiswaanSignaturePath());
            @endphp
            <table class="signatures">
                <tr>
                    <td>
                        <div class="sign-space">
                            @if($principalSignature)
                                <img src="{{ $principalSignature }}" alt="Tanda Tangan Kepala Sekolah">
                            @endif
                        </div>
                        <div class="sign-line">
                            {{ $school?->principalName() ?? '_________________' }}
                            <div class="sign-role">Kepala Sekolah</div>
                        </div>
                    </td>
                    <td>
                        <div class="sign-space">
                            @if($kesiswaanSignature)
                                <img src="{{ $kesiswaanSignature }}" alt="Tanda Tangan Guru Kesiswaan">
                            @endif
                        </div>
                        <div class="sign-line">
                            {{ $school?->kesiswaanName() ?? '_________________' }}
                            <div class="sign-role">Guru Kesiswaan</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="footer-note">
                Diterbitkan pada {{ $certificate->issued_at->translatedFormat('d F Y') }} melalui {{ website_name() }}
            </div>

            <div class="cert-number">No. {{ $certificate->certificate_number }}</div>
        </div>
    </div>
</body>
</html>
