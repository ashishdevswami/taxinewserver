@extends('admin.layouts.web_header');

<style>
    .flickity-page-dots .dot.is-selected {
        background-color: #2bc9de;
    }



    #welcome:before {
        position: absolute;
        content: " ";
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: block;
        z-index: 0;
        /* background-image: linear-gradient(#00000099, #0009); */
        background-color: #ffffff;
    }

  

</style>

<div id="home" class="container-fluid mt-10">

    <div class="row bg-cover" data-jarallax data-speed=".8" style="background: {{ $data->firstrowbgcolor }} !important;background-image: url(img/pattern-1.svg)">
        <div class="col-md-6 p-0">
            <img src="https://jag.cab/assets/website/assets/img/slider3.webp" alt="" class="w-100">
        </div>
        <div class="col-md-6 py-10 py-md-0  m-auto">
            <div class="text-center">
            @if($data)
            
                <h1 class="text-white">
                  {!! $data->description !!}   
                </h1>
            
            @endif            
                <div class="row">
                    <div class="col-md-6 mb-5 text-md-right">
                        <a href="{{ url($data->userioslink) }}" target="_blank">
                            <img src="data:image/png;base64,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" alt="" class="w-50 w-md-75 wow slideInLeft">
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

</div>

<!-- 
<section class="py-15 mt-10" id="welcome" data-jarallax data-speed=".8" style="background-image: url(img/1.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-7 text-white ml-auto">
                <h2>
                    It’s time to change your ride experience!
                    Download the app for free
                </h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6 mb-5 text-md-right">
                                <img src="img/app-store.svg" alt="" class="w-50 w-md-100 wow slideInLeft">
                            </div>
                            <div class="col-md-6 mb-5 text-md-left">
                                <img src="img/play-store.svg" alt="" class="w-50 w-md-100 wow slideInRight">
                            </div>
                        </div>
                    </div>
                </div>
                <h1>
                    No Mask.No ride
                </h1>
            </div>
        </div>
    </div>
</section> -->
  @if($data)
  

<section class="py-10 bg-light">
    <div class="container">
        <div class="row">

            <div class="col-md-4 m-auto">
                <div class=" dan-card-30 card lift p-5 mb-md-0 ">
                    <div class=" card-img-top position-relative mx-auto " style=" max-width: 120px; ">
                        <img class=" img-fluid " src="https://jag.cab/assets/website/assets/img/user.jpg" alt=" ... ">
                    </div>
                    <div class=" card-body text-center ">
                        <h6 class=" mb-4 text-dark ">
                            {!! $data->firstrowheadtext1 !!}
                        </h6>
                        <p class=" mb-0 text-gray-500 ">
                            {!! $data->firstrowsubtext1 !!}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 m-auto">
                <div class=" dan-card-30 card lift p-5 mb-md-0 ">
                    <div class=" card-img-top position-relative mx-auto " style=" max-width: 120px; ">
                        <img class=" img-fluid " src="https://jag.cab/assets/website/assets/img/user.jpg" alt=" ... ">
                    </div>
                    <div class=" card-body text-center ">
                        <h6 class=" mb-4 text-dark ">
                            {!! $data->firstrowheadtext2 !!}
                        </h6>
                        <p class=" mb-0 text-gray-500 ">
                            {!! $data->firstrowsubtext2 !!}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 m-auto">
                <div class=" dan-card-30 card lift p-5 mb-md-0 ">
                    <div class=" card-img-top position-relative mx-auto " style=" max-width: 120px; ">
                        <img class=" img-fluid " src="https://jag.cab/assets/website/assets/img/user.jpg " alt=" ... ">
                    </div>
                    <div class=" card-body text-center ">
                        <h6 class=" mb-4 text-dark ">
                            {!! $data->firstrowheadtext3 !!}
                        </h6>
                        <p class=" mb-0 text-gray-500 ">
                            {!! $data->firstrowsubtext3 !!}                        
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container dan-slider-2 py-8">
    <div class="row position-relative align-items-center">
        <div class="col-md-5 position-static order-2 order-md-2">
            <!-- Slider -->
            <div class="position-static flickity-buttons-lg" id="sliderArrivals" data-flickity='{"pageDots": true}'>

                <!-- Item -->
                <div class="col-12">
                    <!-- Card -->
                    <div class="card">
                        <h2>
                            {!! $data->secondrowheadtext1 !!}
                        </h2>
                    </div>
                </div>

                <!-- Item -->
                <div class="col-12">
                    <!-- Card -->
                    <div class="card">
                        <h2>
                            {!! $data->secondrowheadtext2 !!}
                        </h2>
                    </div>
                </div>

                <!-- Item -->
                <div class="col-12">
                    <!-- Card -->
                    <div class="card">
                        <h2>
                            {!! $data->secondrowheadtext3 !!}
                        </h2>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-7 order-1 order-md-1">
            <!-- Slider -->
            <div data-flickity='{"fade": true, "asNavFor": "#sliderArrivals", "draggable": false}'>

                <!-- Item -->
                <div class="w-100">
                    <img src="https://jag.cab/assets/website/assets/img/slider3.webp" alt="..." class="w-100">
                </div>

                <!-- Item -->
                <div class="w-100">
                    <img src="https://jag.cab/assets/website/assets/img/slider3.webp" alt="..." class="w-100">
                </div>

                <!-- Item -->
                <div class="w-100">
                    <img src="https://jag.cab/assets/website/assets/img/slider3.webp" alt="..." class="w-100">
                </div>
            </div>
        </div>

    </div>
</div>
<section class="slice slice-lg bg-gradient-primary bg-cover py-10" style="background: var(--logo-gradient);background-image: url('{{ asset($p.$data->afrbimage) }}')">
  <div class="container">
    <div class="mb-5 text-center">
      <h3 class="text-white mt-4">{!! $data->afrheadtext !!}</h3>
      <div class="fluid-paragraph mt-3">
        <!-- <p class="lead lh-180 text-white">Start building fast, beautiful and modern looking websites in no time using our theme.</p> -->
      </div>
    </div>
    <div class="row row-grid align-items-center">
      <div class="col-lg-4">
        <div class="d-flex align-items-start mb-5">
          <div class="pr-4">
            <div class="icon icon-shape bg-white text-primary box-shadow-3 rounded-circle" style="background: {{ $data->hdriverdownloadcolor }} !important;">
              1
            </div>
          </div>
          <div class="icon-text">
            <h5 class="h5 text-white">
              {!! $data->afrstext1 !!}
            </h5>
            <p class="mb-0 text-white"><br><br></p>
          </div>
        </div>
        <div class="d-flex align-items-start">
          <div class="pr-4">
            <div class="icon icon-shape bg-white text-primary box-shadow-3 rounded-circle" style="background: {{ $data->hdriverdownloadcolor }} !important;">
              2
            </div>
          </div>
          <div class="icon-text">
            <h5 class="text-white">
              {!! $data->afrstext2 !!}
            </h5>
            <p class="mb-0 text-white"><br><br></p>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="position-relative" style="z-index: 10;">
          <img alt="Image placeholder" src="{{ asset($p.$data->afrlimage) }}" class="img-center img-fluid">
        </div>
      </div>
      <div class="col-lg-4">
        <div class="d-flex align-items-start mb-5">
          <div class="pr-4">
            <div class="icon icon-shape bg-white text-primary box-shadow-3 rounded-circle" style="background: {{ $data->hdriverdownloadcolor }} !important;">
              3
            </div>
          </div>
          <div class="icon-text">
            <h5 class="text-white">
              {!! $data->afrstext3 !!}
            </h5>
            <p class="mb-0 text-white"><br><br></p>
          </div>
        </div>
        <div class="d-flex align-items-start">
          <div class="pr-4">
            <div class="icon icon-shape bg-white text-primary box-shadow-3 rounded-circle" style="background: {{ $data->hdriverdownloadcolor }} !important;">
              4
            </div>
          </div>
          <div class="icon-text">
            <h5 class="text-white">
              {!! $data->afrstext4 !!}
            </h5>
            <p class="mb-0 text-white"><br><br></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 m-auto">
      <div class="row">
        <div class="col-md-6 mb-5 text-md-right">
          <a href="{{ url($data->driverioslink) }}" target="_blank">
            <img src="{{ asset($p.$data->playstoreicon1) }}" alt="" class="w-50 w-md-50 wow slideInLeft">
          </a>
        </div>
        <div class="col-md-6 mb-5 text-md-left">
          <a href="{{ url($data->driverandroidlink) }}" target="_blank">
            <img src="{{ asset($p.$data->playstoreicon2) }}" alt="" class="w-50 w-md-50 wow slideInRight">
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

@endif
@extends('admin.layouts.web_footer')