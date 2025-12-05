@extends('backend.app')

@section('title', 'Dashboard')

@section('content')
    {{-- PAGE-HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>
    </div>
    {{-- PAGE-HEADER --}}


    <div class="row">
        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/user" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $admins }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Admins</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-primary dash ms-auto box-shadow-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                                        <g data-name="28-Agency">
                                            <path style="fill:#ffee6e" d="M47 11h10v2H47z" />
                                            <path transform="rotate(-29.745 50.5 6)" style="fill:#ffee6e"
                                                d="M46.469 5h8.062v2h-8.062z" />
                                            <path transform="rotate(-60.255 50.5 18)" style="fill:#ffee6e"
                                                d="M49.5 13.969h2v8.062h-2z" />
                                            <path style="fill:#ffee6e" d="M7 11h10v2H7z" />
                                            <path transform="rotate(-60.255 13.5 6)" style="fill:#ffee6e"
                                                d="M12.5 1.969h2v8.062h-2z" />
                                            <path transform="rotate(-29.745 13.499 18)" style="fill:#ffee6e"
                                                d="M9.469 17h8.062v2H9.469z" />
                                            <path
                                                d="m36.707 26.293-1.414 1.414L37.586 30H33v-6h-2v6h-4.586l2.293-2.293-1.414-1.414-4 4A.974.974 0 0 0 23.3 31.7l-.006.006 4 4 1.414-1.414L26.414 32h11.172l-2.293 2.293 1.414 1.414 4-4-.007-.007a.974.974 0 0 0 .006-1.408z"
                                                style="fill:#afb4c8" />
                                            <path style="fill:#b2876d" d="M26 13h4v8h-4z" />
                                            <path style="fill:#966857" d="M28 13h2v8h-2zM27 1v2.73l-4 2.18V1h4z" />
                                            <path style="fill:#b2876d" d="M25 1h-2v4.91l2-1.09V1z" />
                                            <path style="fill:#a3d4ff" d="M33 13h5v4h-5z" />
                                            <path style="fill:#65b1fc" d="M33 15h5v2h-5z" />
                                            <path d="M38 13h-5v4h5zm3-3.09V21H30v-8h-4v8h-3V9.91L32 5z"
                                                style="fill:#f7f7f7" />
                                            <path style="fill:#cfcfcf" d="m32 5-9 4.91v2L32 7l9 4.91v-2L32 5z" />
                                            <path style="fill:#ff936b"
                                                d="M27 3.73 32 1l11 6v4l-2-1.09L32 5l-9 4.91L21 11V7l2-1.09 4-2.18z" />
                                            <path style="fill:#ff7045"
                                                d="m32 3-9 4.91L21 9v2l2-1.09L32 5l9 4.91L43 11V9l-2-1.09L32 3z" />
                                            <path d="M43 31v2h3c7 0 7-3 7-3a2.938 2.938 0 0 0 3 3h3v-2a8 8 0 0 0-16 0z"
                                                style="fill:#966857" />
                                            <path
                                                d="M53 28a2.938 2.938 0 0 0 3 3h3v2h-3a2.938 2.938 0 0 1-3-3s0 3-7 3h-3v-2h3c7 0 7-3 7-3z"
                                                style="fill:#8d5c4d" />
                                            <path style="fill:#e9edf5" d="M39 47v16h24V47l-10-2-2 2-2-2-10 2z" />
                                            <path style="fill:#cdd2e1"
                                                d="m49 45 2 2 2-2 10 2v3l-10-2-2 2-2-2-10 2v-3l10-2z" />
                                            <path
                                                d="M49 42.59V45l2 2 2-2v-2.41a5.083 5.083 0 0 1-4 0zM56 33v5h1a2.006 2.006 0 0 0 2-2v-3zM43 33v3a2.006 2.006 0 0 0 2 2h1v-5z"
                                                style="fill:#faa68e" />
                                            <path
                                                d="M49 42.59a5 5 0 0 0 5.54-1.05A5.022 5.022 0 0 0 56 38v-5a2.938 2.938 0 0 1-3-3s0 3-7 3v5a5.029 5.029 0 0 0 3 4.59z"
                                                style="fill:#ffcdbe" />
                                            <path
                                                d="M53 30a2.938 2.938 0 0 0 3 3v2a2.938 2.938 0 0 1-3-3s0 3-7 3v-2c7 0 7-3 7-3z"
                                                style="fill:#ffbeaa" />
                                            <path style="fill:#a3d4ff" d="m50 52-1 6 2 2 2-2-1-6h-2z" />
                                            <path style="fill:#65b1fc" d="m48 50 2 2h2l2-2-3-3-3 3z" />
                                            <path style="fill:#afb4c8"
                                                d="M46.22 45.56 48 50l3-3-2-2-2.78.56zM51 47l3 3 1.78-4.44L53 45l-2 2z" />
                                            <path d="M5 31v2h3c7 0 7-3 7-3a2.938 2.938 0 0 0 3 3h3v-2a8 8 0 0 0-16 0z"
                                                style="fill:#966857" />
                                            <path
                                                d="M15 28a2.938 2.938 0 0 0 3 3h3v2h-3a2.938 2.938 0 0 1-3-3s0 3-7 3H5v-2h3c7 0 7-3 7-3z"
                                                style="fill:#8d5c4d" />
                                            <path style="fill:#9c9c9c" d="M1 47v16h24V47l-10-2-2 2-2-2-10 2z" />
                                            <path style="fill:#cfcfcf"
                                                d="m11 45 2 2 2-2 10 2v3l-10-2-2 2-2-2-10 2v-3l10-2z" />
                                            <path
                                                d="M11 42.59V45l2 2 2-2v-2.41a5.083 5.083 0 0 1-4 0zM18 33v5h1a2.006 2.006 0 0 0 2-2v-3zM5 33v3a2.006 2.006 0 0 0 2 2h1v-5z"
                                                style="fill:#faa68e" />
                                            <path
                                                d="M11 42.59a5 5 0 0 0 5.54-1.05A5.022 5.022 0 0 0 18 38v-5a2.938 2.938 0 0 1-3-3s0 3-7 3v5a5.029 5.029 0 0 0 3 4.59z"
                                                style="fill:#ffcdbe" />
                                            <path
                                                d="M15 30a2.938 2.938 0 0 0 3 3v2a2.938 2.938 0 0 1-3-3s0 3-7 3v-2c7 0 7-3 7-3z"
                                                style="fill:#ffbeaa" />
                                            <path
                                                d="M25.2 46.02 16 44.18v-.992A6.007 6.007 0 0 0 18.91 39H19a3 3 0 0 0 3-3v-5a9 9 0 0 0-18 0v5a3 3 0 0 0 3 3h.09A6.007 6.007 0 0 0 10 43.188v.992L.8 46.02A1 1 0 0 0 0 47v16a1 1 0 0 0 1 1h24a1 1 0 0 0 1-1V47a1 1 0 0 0-.8-.98zM19 37v-3h1v2a1 1 0 0 1-1 1zM6 36v-2h1v3a1 1 0 0 1-1-1zm2-4H6v-1a7 7 0 0 1 14 0v1h-2a1.883 1.883 0 0 1-2-2 1.019 1.019 0 0 0-1-1.023.979.979 0 0 0-1 .966v.015c-.017.11-.414 2.042-6 2.042zm1 1.979c2.99-.129 4.7-.84 5.687-1.628A3.963 3.963 0 0 0 17 33.874V38a4 4 0 0 1-8 0zM13 44a6 6 0 0 0 1-.09v.676l-1 1-1-1v-.676a6 6 0 0 0 1 .09zM2 47.819l8.671-1.734L12 47.414V62H2zM24 62H14V47.414l1.329-1.329L24 47.819zM63.2 46.02 54 44.18v-.992A6.007 6.007 0 0 0 56.91 39H57a3 3 0 0 0 3-3v-5a9 9 0 0 0-18 0v5a3 3 0 0 0 3 3h.09A6.007 6.007 0 0 0 48 43.188v.992l-9.2 1.84a1 1 0 0 0-.8.98v16a1 1 0 0 0 1 1h24a1 1 0 0 0 1-1V47a1 1 0 0 0-.8-.98zm-14.525.065.915.915-1.221 1.221L47.6 46.3zm3.258 11.572-.933.929-.929-.929.776-4.657h.306zM51.586 51h-1.172l-1-1L51 48.414 52.586 50zm.828-4 .915-.915 1.071.215-.768 1.921zM57 37v-3h1v2a1 1 0 0 1-1 1zm-13-1v-2h1v3a1 1 0 0 1-1-1zm2-4h-2v-1a7 7 0 0 1 14 0v1h-2a1.883 1.883 0 0 1-2-2 1.019 1.019 0 0 0-1-1.023.979.979 0 0 0-1 .966v.015c-.017.11-.414 2.042-6 2.042zm1 1.979c2.99-.129 4.7-.84 5.687-1.628A3.963 3.963 0 0 0 55 33.874V38a4 4 0 0 1-8 0zM51 44a6 6 0 0 0 1-.09v.676l-1 1-1-1v-.676a6 6 0 0 0 1 .09zm-11 3.819 5.6-1.12 1.469 3.672a.978.978 0 0 0 .227.331l-.005.005 1.636 1.636-.915 5.493a1 1 0 0 0 .279.871L50 60.414V62H40zM62 62H52v-1.586l1.707-1.707a1 1 0 0 0 .279-.871l-.915-5.493 1.636-1.636-.007-.007a.978.978 0 0 0 .227-.331L56.4 46.7l5.6 1.12zM20.489 11.86a1 1 0 0 0 .99.018l.521-.285V21a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-9.407l.521.285A1 1 0 0 0 44 11V7a1 1 0 0 0-.521-.878l-11-6a1 1 0 0 0-.958 0L28 2.043V1a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v4.316l-1.479.806A1 1 0 0 0 20 7v4a1 1 0 0 0 .489.86zM27 20v-6h2v6zm13 0h-9v-7a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v7h-1v-9.5l8-4.364 8 4.364zM24 2h2v1.134l-2 1.091zm-2 5.594 10-5.455 10 5.455v1.721l-9.521-5.193a1 1 0 0 0-.958 0L22 9.315z" />
                                            <path
                                                d="M32 17a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-5a1 1 0 0 0-1 1zm2-3h3v2h-3zM47 11h10v2H47zM46.504 7.132l7-4 .992 1.737-7 4zM46.504 16.869l.993-1.737 7 4-.993 1.736zM7 11h10v2H7zM9.504 4.868l.993-1.737 7 4-.993 1.737zM9.504 19.131l7-4 .992 1.737-7 4zM40.7 31.7a.974.974 0 0 0 .006-1.408l-4-4-1.414 1.414L37.586 30H33v-6h-2v6h-4.586l2.293-2.293-1.414-1.414-4 4A.974.974 0 0 0 23.3 31.7l-.006.006 4 4 1.414-1.414L26.414 32h11.172l-2.293 2.293 1.414 1.414 4-4z" />
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold">{{ $users }}</h3>
                            <p class="text-muted fs-13 mb-0">Total Users (Without Admin)</p>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-secondary dash ms-auto box-shadow-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path
                                        d="M160 64C142.3 64 128 78.3 128 96C128 113.7 142.3 128 160 128L160 139C160 181.4 176.9 222.1 206.9 252.1L274.8 320L206.9 387.9C176.9 417.9 160 458.6 160 501L160 512C142.3 512 128 526.3 128 544C128 561.7 142.3 576 160 576L480 576C497.7 576 512 561.7 512 544C512 526.3 497.7 512 480 512L480 501C480 458.6 463.1 417.9 433.1 387.9L365.2 320L433.1 252.1C463.1 222.1 480 181.4 480 139L480 128C497.7 128 512 113.7 512 96C512 78.3 497.7 64 480 64L160 64zM224 139L224 128L416 128L416 139C416 158 410.4 176.4 400 192L240 192C229.7 176.4 224 158 224 139zM240 448C243.5 442.7 247.6 437.7 252.1 433.1L320 365.2L387.9 433.1C392.5 437.7 396.5 442.7 400.1 448L240 448z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@if(Auth::user()->role==='Admin')
        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/store" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $pendingStores }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Pending Stores</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-warning dash ms-auto box-shadow-info">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path
                                            d="M53.5 245.1L110.3 131.4C121.2 109.7 143.3 96 167.6 96L472.5 96C496.7 96 518.9 109.7 529.7 131.4L586.5 245.1C590.1 252.3 592 260.2 592 268.3C592 295.6 570.8 318 544 319.9L544 512C544 529.7 529.7 544 512 544C494.3 544 480 529.7 480 512L480 320L384 320L384 496C384 522.5 362.5 544 336 544L144 544C117.5 544 96 522.5 96 496L96 319.9C69.2 318 48 295.6 48 268.3C48 260.3 49.9 252.3 53.5 245.1zM160 320L160 432C160 440.8 167.2 448 176 448L304 448C312.8 448 320 440.8 320 432L320 320L160 320z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/store" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $activeStores }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Active Stores</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-success dash ms-auto box-shadow-info">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path
                                            d="M53.5 245.1L110.3 131.4C121.2 109.7 143.3 96 167.6 96L472.5 96C496.7 96 518.9 109.7 529.7 131.4L586.5 245.1C590.1 252.3 592 260.2 592 268.3C592 295.6 570.8 318 544 319.9L544 512C544 529.7 529.7 544 512 544C494.3 544 480 529.7 480 512L480 320L384 320L384 496C384 522.5 362.5 544 336 544L144 544C117.5 544 96 522.5 96 496L96 319.9C69.2 318 48 295.6 48 268.3C48 260.3 49.9 252.3 53.5 245.1zM160 320L160 432C160 440.8 167.2 448 176 448L304 448C312.8 448 320 440.8 320 432L320 320L160 320z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/product" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $activeProducts }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Active Products</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-success dash ms-auto box-shadow-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                        <path
                                            d="M439.4 96L448 96C483.3 96 512 124.7 512 160L512 512C512 547.3 483.3 576 448 576L192 576C156.7 576 128 547.3 128 512L128 160C128 124.7 156.7 96 192 96L200.6 96C211.6 76.9 232.3 64 256 64L384 64C407.7 64 428.4 76.9 439.4 96zM376 176C389.3 176 400 165.3 400 152C400 138.7 389.3 128 376 128L264 128C250.7 128 240 138.7 240 152C240 165.3 250.7 176 264 176L376 176zM256 320C256 302.3 241.7 288 224 288C206.3 288 192 302.3 192 320C192 337.7 206.3 352 224 352C241.7 352 256 337.7 256 320zM288 320C288 333.3 298.7 344 312 344L424 344C437.3 344 448 333.3 448 320C448 306.7 437.3 296 424 296L312 296C298.7 296 288 306.7 288 320zM288 448C288 461.3 298.7 472 312 472L424 472C437.3 472 448 461.3 448 448C448 434.7 437.3 424 424 424L312 424C298.7 424 288 434.7 288 448zM224 480C241.7 480 256 465.7 256 448C256 430.3 241.7 416 224 416C206.3 416 192 430.3 192 448C192 465.7 206.3 480 224 480z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>


        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/product" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $inactiveProducts }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Inactive Products</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-danger dash ms-auto box-shadow-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                        <path
                                            d="M439.4 96L448 96C483.3 96 512 124.7 512 160L512 512C512 547.3 483.3 576 448 576L192 576C156.7 576 128 547.3 128 512L128 160C128 124.7 156.7 96 192 96L200.6 96C211.6 76.9 232.3 64 256 64L384 64C407.7 64 428.4 76.9 439.4 96zM376 176C389.3 176 400 165.3 400 152C400 138.7 389.3 128 376 128L264 128C250.7 128 240 138.7 240 152C240 165.3 250.7 176 264 176L376 176zM256 320C256 302.3 241.7 288 224 288C206.3 288 192 302.3 192 320C192 337.7 206.3 352 224 352C241.7 352 256 337.7 256 320zM288 320C288 333.3 298.7 344 312 344L424 344C437.3 344 448 333.3 448 320C448 306.7 437.3 296 424 296L312 296C298.7 296 288 306.7 288 320zM288 448C288 461.3 298.7 472 312 472L424 472C437.3 472 448 461.3 448 448C448 434.7 437.3 424 424 424L312 424C298.7 424 288 434.7 288 448zM224 480C241.7 480 256 465.7 256 448C256 430.3 241.7 416 224 416C206.3 416 192 430.3 192 448C192 465.7 206.3 480 224 480z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/payment" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $activeSubscritpions ?? '0' }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Active Subscriptions</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-success dash ms-auto box-shadow-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                        <path
                                            d="M64 192L64 224L576 224L576 192C576 156.7 547.3 128 512 128L128 128C92.7 128 64 156.7 64 192zM64 272L64 448C64 483.3 92.7 512 128 512L512 512C547.3 512 576 483.3 576 448L576 272L64 272zM128 424C128 410.7 138.7 400 152 400L200 400C213.3 400 224 410.7 224 424C224 437.3 213.3 448 200 448L152 448C138.7 448 128 437.3 128 424zM272 424C272 410.7 282.7 400 296 400L360 400C373.3 400 384 410.7 384 424C384 437.3 373.3 448 360 448L296 448C282.7 448 272 437.3 272 424z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/payment" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $inactiveSubscritpions ?? '0' }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Inactive Subscriptions</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-danger dash ms-auto box-shadow-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                        <path
                                            d="M64 192L64 224L576 224L576 192C576 156.7 547.3 128 512 128L128 128C92.7 128 64 156.7 64 192zM64 272L64 448C64 483.3 92.7 512 128 512L512 512C547.3 512 576 483.3 576 448L576 272L64 272zM128 424C128 410.7 138.7 400 152 400L200 400C213.3 400 224 410.7 224 424C224 437.3 213.3 448 200 448L152 448C138.7 448 128 437.3 128 424zM272 424C272 410.7 282.7 400 296 400L360 400C373.3 400 384 410.7 384 424C384 437.3 373.3 448 360 448L296 448C282.7 448 272 437.3 272 424z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/plan" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $plans ?? '0' }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Active Plans</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-info dash ms-auto box-shadow-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                        <path
                                            d="M424.5 355.1C449 329.2 464 294.4 464 256C464 176.5 399.5 112 320 112C240.5 112 176 176.5 176 256C176 294.4 191 329.2 215.5 355.1C236.8 377.5 260.4 409.1 268.8 448L371.2 448C379.6 409 403.2 377.5 424.5 355.1zM459.3 388.1C435.7 413 416 443.4 416 477.7L416 496C416 540.2 380.2 576 336 576L304 576C259.8 576 224 540.2 224 496L224 477.7C224 443.4 204.3 413 180.7 388.1C148 353.7 128 307.2 128 256C128 150 214 64 320 64C426 64 512 150 512 256C512 307.2 492 353.7 459.3 388.1zM272 248C272 261.3 261.3 272 248 272C234.7 272 224 261.3 224 248C224 199.4 263.4 160 312 160C325.3 160 336 170.7 336 184C336 197.3 325.3 208 312 208C289.9 208 272 225.9 272 248z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
@endif
        <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
            <a href="/admin/mail" class="card-link">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ $email ?? '0' }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Sent Emails</p>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-primary dash ms-auto box-shadow-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                                        viewBox="0 0 640 640">
                                        <path
                                            d="M112 128C85.5 128 64 149.5 64 176C64 191.1 71.1 205.3 83.2 214.4L291.2 370.4C308.3 383.2 331.7 383.2 348.8 370.4L556.8 214.4C568.9 205.3 576 191.1 576 176C576 149.5 554.5 128 528 128L112 128zM64 260L64 448C64 483.3 92.7 512 128 512L512 512C547.3 512 576 483.3 576 448L576 260L377.6 408.8C343.5 434.4 296.5 434.4 262.4 408.8L64 260z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
            </a>
        </div>
    </div>
    <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
        <a href="/admin/coupon" class="card-link">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold">{{ $coupon ?? '0' }}</h3>
                            <p class="text-muted fs-13 mb-0">Total Active Coupon</p>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-success dash ms-auto box-shadow-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 128C60.7 128 32 156.7 32 192L32 256C32 264.8 39.4 271.7 47.7 274.6C66.5 281.1 80 299 80 320C80 341 66.5 358.9 47.7 365.4C39.4 368.3 32 375.2 32 384L32 448C32 483.3 60.7 512 96 512L544 512C579.3 512 608 483.3 608 448L608 384C608 375.2 600.6 368.3 592.3 365.4C573.5 358.9 560 341 560 320C560 299 573.5 281.1 592.3 274.6C600.6 271.7 608 264.8 608 256L608 192C608 156.7 579.3 128 544 128L96 128zM448 400L448 240L192 240L192 400L448 400zM144 224C144 206.3 158.3 192 176 192L464 192C481.7 192 496 206.3 496 224L496 416C496 433.7 481.7 448 464 448L176 448C158.3 448 144 433.7 144 416L144 224z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
        <a href="/admin/review" class="card-link">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold">{{ $review ?? '0' }}</h3>
                            <p class="text-muted fs-13 mb-0">Total Active Reviews</p>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-primary dash ms-auto box-shadow-info">
                               <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 640 640"><path d="M341.5 45.1C337.4 37.1 329.1 32 320.1 32C311.1 32 302.8 37.1 298.7 45.1L225.1 189.3L65.2 214.7C56.3 216.1 48.9 222.4 46.1 231C43.3 239.6 45.6 249 51.9 255.4L166.3 369.9L141.1 529.8C139.7 538.7 143.4 547.7 150.7 553C158 558.3 167.6 559.1 175.7 555L320.1 481.6L464.4 555C472.4 559.1 482.1 558.3 489.4 553C496.7 547.7 500.4 538.8 499 529.8L473.7 369.9L588.1 255.4C594.5 249 596.7 239.6 593.9 231C591.1 222.4 583.8 216.1 574.8 214.7L415 189.3L341.5 45.1z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
        <a href="/admin/enquiry" class="card-link">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold">{{ $enquiry ?? '0' }}</h3>
                            <p class="text-muted fs-13 mb-0">Total Pending Enquiry</p>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-warning dash ms-auto box-shadow-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 640 640"><path d="M528 320C528 434.9 434.9 528 320 528C205.1 528 112 434.9 112 320C112 205.1 205.1 112 320 112C434.9 112 528 205.1 528 320zM64 320C64 461.4 178.6 576 320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320zM296 184L296 320C296 328 300 335.5 306.7 340L402.7 404C413.7 411.4 428.6 408.4 436 397.3C443.4 386.2 440.4 371.4 429.3 364L344 307.2L344 184C344 170.7 333.3 160 320 160C306.7 160 296 170.7 296 184z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
        <a href="/admin/faq" class="card-link">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold">{{ $faq ?? '0' }}</h3>
                            <p class="text-muted fs-13 mb-0">Total Active FAQs</p>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-success dash ms-auto box-shadow-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 640 640"><path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM320 240C302.3 240 288 254.3 288 272C288 285.3 277.3 296 264 296C250.7 296 240 285.3 240 272C240 227.8 275.8 192 320 192C364.2 192 400 227.8 400 272C400 319.2 364 339.2 344 346.5L344 350.3C344 363.6 333.3 374.3 320 374.3C306.7 374.3 296 363.6 296 350.3L296 342.2C296 321.7 310.8 307 326.1 302C332.5 299.9 339.3 296.5 344.3 291.7C348.6 287.5 352 281.7 352 272.1C352 254.4 337.7 240.1 320 240.1zM288 432C288 414.3 302.3 400 320 400C337.7 400 352 414.3 352 432C352 449.7 337.7 464 320 464C302.3 464 288 449.7 288 432z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
        <a href="/admin/activity-logs" class="card-link">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold">{{ $activityLog ?? '0' }}</h3>
                            <p class="text-muted fs-13 mb-0">Total Activity Logs</p>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-info dash ms-auto box-shadow-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 640 640"><path d="M128 128C128 110.3 113.7 96 96 96C78.3 96 64 110.3 64 128L64 464C64 508.2 99.8 544 144 544L544 544C561.7 544 576 529.7 576 512C576 494.3 561.7 480 544 480L144 480C135.2 480 128 472.8 128 464L128 128zM534.6 214.6C547.1 202.1 547.1 181.8 534.6 169.3C522.1 156.8 501.8 156.8 489.3 169.3L384 274.7L326.6 217.4C314.1 204.9 293.8 204.9 281.3 217.4L185.3 313.4C172.8 325.9 172.8 346.2 185.3 358.7C197.8 371.2 218.1 371.2 230.6 358.7L304 285.3L361.4 342.7C373.9 355.2 394.2 355.2 406.7 342.7L534.7 214.7z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
@endsection
