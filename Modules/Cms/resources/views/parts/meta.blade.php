<meta charset="utf-8" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="@yield('description', appDescription())">
<meta name="keywords" content="@yield('keywords', appKeywords())">
<meta name="author" content="@yield('author', setting('site_author'))">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{url()->current()}}" />
<title>@yield('title', appTitle())</title>
<meta property='article:published_time' content='@yield('date', \Carbon\Carbon::now()->toDateTimeString())'>
<meta property='article:section' content='@yield('category', 'news')'>

<meta property="og:site_name" content="{{ app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name') }}">
<meta property="og:type" content="@yield('type', 'website')" />
<meta property="og:locale" content="ar-eg" />
<meta property="og:locale:alternate" content="en-us">
<meta property="og:title" content="@yield('title', appTitle())" />
<meta property="og:description" content="@yield('description', appDescription())" />
<meta property="og:image" content="@yield('image', url('storage/' . setting('site_profile')))" />
<meta property="og:image:alt" content="@yield('title', appTitle())" />
<meta property="og:url" content="{{url()->current()}}" />
<meta property="fb:app_id" content="{{ config('services.facebook.app_id') }}" />

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="@yield('title', appTitle())">
<meta name="twitter:description" content="@yield('description', appDescription())">
<meta name="twitter:image" content="@yield('image', url('storage/' . setting('site_profile')))">
<meta name="twitter:site" content="@engfadymondy">
<meta name="twitter:creator" content="@engfadymondy">


<script type="application/ld+json">{"@context":"https://schema.org","@type":"Article","name":"{{ appTitle() }}"}</script>

