{{--
  Template Name: WooCommerce Template
--}}

@extends('layouts.app')

@section('content')
  @while (have_posts()) @php(the_post())
    @php(woocommerce_content())
  @endwhile
@endsection
