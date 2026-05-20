{{--
    File: resources/views/stores/show.blade.php
    
    ============================================
    DATA YANG DIBUTUHKAN DARI BACKEND:
    ============================================
    - $store -> id, name, slug, description, image, city, is_online, created_at
    - $products (paginated) -> id, name, slug, image, price, original_price, quantity, sold_count
    - $categories (atau $storeCategories) -> id, name, slug
    - $currentCategory (optional) -> category yang sedang difilter
--}}

@extends('layouts.app')

@section('title', ($store->name ?? 'Toko') . ' - TokoKu')

{{-- Sembunyikan category nav global agar fokus ke navigasi internal toko --}}
@section('show-category-nav', false)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    
    {{-- ==================== 1. BANNER & HEADER TOKO ==================== --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        {{-- Store Banner Cover --}}
        <div class="h-32 sm:h-48 md:h-64 relative bg-gradient-to-r from-green-400 to-emerald-600">
            {{-- Aesthetic Abstract Pattern --}}
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="absolute bottom-4 right-4 text-white/50 text-xs font-mono hidden sm:block">TokoKu Verified Merchant</div>
        </div>

        {{-- Store Profile Header --}}
        <div class="p-4 sm:p-6 relative">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 -mt-16 sm:-mt-20 md:-mt-24 relative z-10">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    {{-- Store Logo / Avatar --}}
                    <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-xl bg-white p-1 shadow-md border border-gray-100 flex-shrink-0">
                        @if($store->image)
                            <img src="{{ asset('images/store/' . $store->image) }}" alt="Logo {{ $store->name }}" class="w-full h-full object-cover rounded-lg">
                        @else
                            {{-- Default Initial Logo jika image kosong --}}
                            <div class="w-full h-full bg-emerald-100 text-emerald-700 rounded-lg flex items-center justify-center font-bold text-3xl">
                                {{ strtoupper(substr($store->name ?? 'T', 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Store Meta --}}
                    <div class="pt-2 sm:pt-6">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $store->name ?? 'Nama Toko' }}</h1>
                            
                            {{-- Status Online/Offline --}}
                            @if($store->is_online)
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                    Online
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded-full">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                    Offline
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 flex items-center gap-1 mt-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $store->city ?? 'Lokasi Belum Diatur' }}
                        </p>

                        {{-- Store Stats --}}
                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-600 flex-wrap">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <strong class="text-gray-900">4.8</strong> (Rating Toko)
                            </span>
                            <span class="text-gray-300">|</span>
                            <span>Bergabung Sejak <strong class="text-gray-900">{{ $store->created_at ? $store->created_at->format('M Y') : '2026' }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Store Actions --}}
                <div class="flex items-center gap-2 pt-2 md:pt-8 self-end md:self-center w-full md:w-auto">
                    <button 
                        type="button" 
                        onclick="toggleFollowStore({{ $store->id }})"
                        id="followBtn"
                        class="flex-1 md:flex-none px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-sm transition text-center shadow-sm animate-none"
                    >
                        Ikuti
                    </button>
                    <a 
                        href="https://wa.me/#" 
                        target="_blank"
                        class="flex-1 md:flex-none px-6 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg text-sm transition text-center"
                    >
                        Hubungi Toko
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== 2. NAVIGASI TABS TOKO ==================== --}}
    <div class="border-b border-gray-200 mb-6 bg-white rounded-lg shadow-sm px-2">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button 
                onclick="switchStoreTab('products')"
                id="tab-btn-products" 
                class="border-b-2 border-green-600 text-green-600 py-4 px-1 text-sm font-semibold whitespace-nowrap store-tab-trigger"
            >
                Produk Toko
            </button>
            <button 
                onclick="switchStoreTab('info')"
                id="tab-btn-info" 
                class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap store-tab-trigger"
            >
                Informasi Toko
            </button>
        </nav>
    </div>

    {{-- ==================== 3. TAB KONTEN: PRODUK TOKO (Sesuai dengan index.blade.php) ==================== --}}
    <div id="tab-content-products" class="store-tab-panel">
        <div class="lg:flex lg:gap-6">
            
            {{-- ========== SIDEBAR FILTER (Desktop) ========== --}}
            <aside class="hidden lg:block w-56 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm p-4 sticky top-24">
                    <h3 class="font-semibold text-gray-800 mb-4">Filter</h3>
                    
                    {{-- Category Filter --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Kategori</h4>
                        <ul class="space-y-2">
                            <li>
                                <a 
                                    href="{{ route('stores.show', $store->slug) }}" 
                                    class="text-sm {{ !request('category') ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600' }}"
                                >
                                    Semua Kategori
                                </a>
                            </li>
                            @foreach($categories ?? $storeCategories ?? [] as $category)
                                <li>
                                    <a 
                                        href="{{ route('stores.show', [$store->slug, 'category' => $category->slug]) }}" 
                                        class="text-sm {{ request('category') == $category->slug ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600' }}"
                                    >
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    {{-- Price Range Filter --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Harga</h4>
                        <div class="space-y-2">
                            <input 
                                type="number" 
                                name="price_min" 
                                placeholder="Harga Minimum"
                                value="{{ request('price_min') }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                            >
                            <input 
                                type="number" 
                                name="price_max" 
                                placeholder="Harga Maksimum"
                                value="{{ request('price_max') }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                            >
                            <button 
                                type="button"
                                onclick="applyPriceFilter()"
                                class="w-full py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700"
                            >
                                Terapkan
                            </button>
                        </div>
                    </div>
                    
                    {{-- Rating Filter --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Rating</h4>
                        <div class="space-y-2">
                            @for($i = 4; $i >= 1; $i--)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="rating" 
                                        value="{{ $i }}"
                                        {{ request('rating') == $i ? 'checked' : '' }}
                                        onchange="applyFilter('rating', this.checked ? {{ $i }} : '')"
                                        class="rounded text-green-600 focus:ring-green-500 border-gray-300"
                                    >
                                    <div class="flex items-center gap-1">
                                        @for($j = 1; $j <= 5; $j++)
                                            <svg class="w-4 h-4 {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="text-xs text-gray-500">ke atas</span>
                                    </div>
                                </label>
                            @endfor
                        </div>
                    </div>
                    
                    {{-- Location Filter --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Lokasi</h4>
                        <select 
                            name="location"
                            onchange="applyFilter('location', this.value)"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                        >
                            <option value="">Semua Lokasi</option>
                            <option value="jakarta" {{ request('location') == 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                            <option value="bandung" {{ request('location') == 'bandung' ? 'selected' : '' }}>Bandung</option>
                            <option value="surabaya" {{ request('location') == 'surabaya' ? 'selected' : '' }}>Surabaya</option>
                            <option value="medan" {{ request('location') == 'medan' ? 'selected' : '' }}>Medan</option>
                            <option value="yogyakarta" {{ request('location') == 'yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                        </select>
                    </div>
                </div>
            </aside>
            
            {{-- ========== MAIN CONTENT (Produk Grid) ========== --}}
            <div class="flex-1">
                
                {{-- Header & Sort --}}
                <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        {{-- Title & Count --}}
                        <div>
                            <h1 class="text-lg font-semibold text-gray-800">
                                {{ $currentCategory->name ?? 'Semua Produk' }}
                            </h1>
                            <p class="text-sm text-gray-500">
                                Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                            </p>
                        </div>
                        
                        {{-- Sort & Filter Mobile Button --}}
                        <div class="flex items-center gap-2">
                            {{-- Mobile Filter Button --}}
                            <button 
                                type="button"
                                onclick="openMobileFilter()"
                                class="lg:hidden flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </button>
                            
                            {{-- Sort Dropdown --}}
                            <select 
                                onchange="applyFilter('sort', this.value)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-green-500"
                            >
                                <option value="">Urutkan</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="best_seller" {{ request('sort') == 'best_seller' ? 'selected' : '' }}>Terlaris</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                {{-- Active Filters --}}
                @if(request()->hasAny(['category', 'price_min', 'price_max', 'rating', 'location']))
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="text-sm text-gray-600">Filter aktif:</span>
                        
                        @if(request('category'))
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                                {{ $currentCategory->name ?? request('category') }}
                                <a href="{{ request()->fullUrlWithoutQuery('category') }}" class="hover:text-green-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            </span>
                        @endif
                        
                        @if(request('price_min') || request('price_max'))
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                                Rp {{ number_format(request('price_min', 0), 0, ',', '.') }} - Rp {{ number_format(request('price_max', 999999999), 0, ',', '.') }}
                                <a href="{{ request()->fullUrlWithoutQuery(['price_min', 'price_max']) }}" class="hover:text-green-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            </span>
                        @endif
                        
                        @if(request('rating'))
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                                Rating {{ request('rating') }}+ ⭐
                                <a href="{{ request()->fullUrlWithoutQuery('rating') }}" class="hover:text-green-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            </span>
                        @endif

                        @if(request('location'))
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full capitalize">
                                {{ request('location') }}
                                <a href="{{ request()->fullUrlWithoutQuery('location') }}" class="hover:text-green-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            </span>
                        @endif
                        
                        <a href="{{ route('stores.show', $store->slug) }}" class="text-sm text-red-500 hover:text-red-600">
                            Hapus semua filter
                        </a>
                    </div>
                @endif
                
                {{-- Product Grid --}}
                @if($products->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3 lg:gap-4">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition group">
                                <a href="{{ route('products.show', $product->slug) }}" class="block">
                                    {{-- Product Image --}}
                                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                        @if($product->image)
                                            <img 
                                                src="{{ asset('images/product/' . $product->image) }}" 
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100 text-xs font-medium">
                                                Tidak Ada Foto
                                            </div>
                                        @endif
                                        
                                        {{-- Discount Badge --}}
                                        @if($product->original_price && $product->original_price > $product->price)
                                            @php
                                                $discount = round((($product->original_price - $product->price) / $product->original_price) * 100);
                                            @endphp
                                            <span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-semibold px-1.5 py-0.5 rounded">
                                                {{ $discount }}%
                                            </span>
                                        @endif

                                        {{-- Out of Stock Badge --}}
                                        @if($product->quantity <= 0)
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                                <span class="bg-black/80 text-white text-[10px] sm:text-xs px-2.5 py-1 rounded-full font-bold">
                                                    Stok Habis
                                                </span>
                                            </div>
                                        @endif
                                        
                                        {{-- Wishlist Button --}}
                                        <button 
                                            type="button"
                                            onclick="event.preventDefault(); toggleWishlist({{ $product->id }})"
                                            class="absolute top-2 right-2 w-8 h-8 bg-white/80 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:bg-white"
                                        >
                                            <svg class="w-5 h-5 text-gray-600 wishlist-icon-{{ $product->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    {{-- Product Info --}}
                                    <div class="p-3">
                                        <h3 class="text-sm text-gray-700 line-clamp-2 mb-1 group-hover:text-green-600 min-h-[2.5rem]">
                                            {{ $product->name }}
                                        </h3>
                                        
                                        {{-- Price --}}
                                        <p class="text-sm font-bold text-gray-900">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                        @if($product->original_price && $product->original_price > $product->price)
                                            <p class="text-xs text-gray-400 line-through">
                                                Rp {{ number_format($product->original_price, 0, ',', '.') }}
                                            </p>
                                        @endif
                                        
                                        {{-- Rating & Sold --}}
                                        <div class="flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <span class="text-xs text-gray-500">{{ number_format($product->rating ?? 4.8, 1) }}</span>
                                            <span class="text-xs text-gray-300">|</span>
                                            <span class="text-xs text-gray-500">{{ $product->sold_count ?? 0 }} terjual</span>
                                        </div>
                                        
                                        {{-- Store Location --}}
                                        <p class="text-xs text-gray-400 mt-1 truncate">
                                            {{ $store->city ?? 'Indonesia' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center border border-gray-100">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Produk tidak ditemukan</h3>
                        <p class="text-gray-500 mb-4">Coba ubah filter atau kata kunci pencarian toko kami.</p>
                        <a href="{{ route('stores.show', $store->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition">
                            Reset Filter Toko
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ==================== 4. TAB KONTEN: INFORMASI TOKO ==================== --}}
    <div id="tab-content-info" class="store-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Deskripsi Toko</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        {{ $store->description ?? 'Selamat datang di toko resmi kami! Kami menyediakan produk-produk berkualitas tinggi yang diproduksi secara terpercaya. Semua pengiriman dijamin aman sampai ke tangan pelanggan. Layanan konsultasi chat aktif untuk melayani kebutuhan berbelanja Anda.' }}
                    </p>
                    <div class="border-t border-gray-100 pt-4 mt-4">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Lokasi Utama Toko</h4>
                        <p class="text-sm text-gray-600">{{ $store->city ?? 'Lokasi Belum Diatur' }}, Indonesia</p>
                    </div>
                </div>
                
                {{-- Catatan Merchant --}}
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Info Penting Toko</h3>
                    <div class="space-y-4 text-sm text-gray-600">
                        <div>
                            <h4 class="font-bold text-gray-800">🚚 Kebijakan Pengiriman</h4>
                            <p class="text-xs mt-1 leading-relaxed">Pesanan diproses sesuai urutan masuk. Jika toko online, pertanyaan seputar stock atau pengiriman instan akan segera dibalas oleh tim kami.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">📦 Ketentuan Klaim Pengembalian</h4>
                            <p class="text-xs mt-1 leading-relaxed">Mohon sertakan video unboxing paket lengkap tanpa jeda untuk melakukan pengembalian barang jika terjadi cacat atau salah kirim.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ========== MOBILE FILTER MODAL (Sesuai dengan index.blade.php) ========== --}}
<div id="mobileFilterModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeMobileFilter()"></div>
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[80vh] overflow-hidden z-10">
        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white">
            <h3 class="font-semibold text-gray-800">Filter</h3>
            <button onclick="closeMobileFilter()" class="p-2 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Filter Content --}}
        <div class="p-4 overflow-y-auto max-h-[calc(80vh-120px)]">
            {{-- Category --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Kategori</h4>
                <div class="flex flex-wrap gap-2">
                    <button 
                        onclick="applyFilter('category', '')"
                        class="px-3 py-1.5 text-sm rounded-full {{ !request('category') ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}"
                    >
                        Semua
                    </button>
                    @foreach($categories ?? $storeCategories ?? [] as $category)
                        <button 
                            onclick="applyFilter('category', '{{ $category->slug }}')"
                            class="px-3 py-1.5 text-sm rounded-full {{ request('category') == $category->slug ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
            
            {{-- Price Range --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Rentang Harga</h4>
                <div class="grid grid-cols-2 gap-3">
                    <input 
                        type="number" 
                        id="mobile_price_min"
                        placeholder="Min"
                        value="{{ request('price_min') }}"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg"
                    >
                    <input 
                        type="number" 
                        id="mobile_price_max"
                        placeholder="Max"
                        value="{{ request('price_max') }}"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg"
                    >
                </div>
            </div>
            
            {{-- Rating --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Rating</h4>
                <div class="flex flex-wrap gap-2">
                    @for($i = 4; $i >= 1; $i--)
                        <button 
                            onclick="applyFilter('rating', {{ $i }})"
                            class="flex items-center gap-1 px-3 py-1.5 text-sm rounded-full {{ request('rating') == $i ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}"
                        >
                            {{ $i }}+ ⭐
                        </button>
                    @endfor
                </div>
            </div>
        </div>
        
        {{-- Footer Actions --}}
        <div class="p-4 border-t bg-white sticky bottom-0">
            <div class="grid grid-cols-2 gap-3">
                <button 
                    onclick="resetFilters()"
                    class="py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium"
                >
                    Reset
                </button>
                <button 
                    onclick="applyMobileFilters()"
                    class="py-2.5 bg-green-600 text-white rounded-lg font-medium"
                >
                    Terapkan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ==================== 1. TABS SWITCHER SYSTEM ====================
    function switchStoreTab(tabId) {
        // Hide all panels
        document.querySelectorAll('.store-tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });
        // Show selected panel
        document.getElementById('tab-content-' + tabId).classList.remove('hidden');

        // Style buttons
        document.querySelectorAll('.store-tab-trigger').forEach(btn => {
            btn.classList.remove('border-green-600', 'text-green-600', 'font-semibold');
            btn.classList.add('border-transparent', 'text-gray-500', 'font-medium');
        });

        const activeBtn = document.getElementById('tab-btn-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-gray-500', 'font-medium');
        activeBtn.classList.add('border-green-600', 'text-green-600', 'font-semibold');
    }

    // ==================== 2. FILTER & SEARCH (Exactly aligned with index.blade.php) ====================
    // Apply single filter
    function applyFilter(key, value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        window.location.href = url.toString();
    }
    
    // Apply price filter
    function applyPriceFilter() {
        const url = new URL(window.location.href);
        const min = document.querySelector('input[name="price_min"]').value;
        const max = document.querySelector('input[name="price_max"]').value;
        
        if (min) url.searchParams.set('price_min', min);
        else url.searchParams.delete('price_min');
        
        if (max) url.searchParams.set('price_max', max);
        else url.searchParams.delete('price_max');
        
        window.location.href = url.toString();
    }
    
    // Mobile filter functions
    function openMobileFilter() {
        document.getElementById('mobileFilterModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMobileFilter() {
        document.getElementById('mobileFilterModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    function applyMobileFilters() {
        const url = new URL(window.location.href);
        const min = document.getElementById('mobile_price_min').value;
        const max = document.getElementById('mobile_price_max').value;
        
        if (min) url.searchParams.set('price_min', min);
        else url.searchParams.delete('price_min');
        
        if (max) url.searchParams.set('price_max', max);
        else url.searchParams.delete('price_max');
        
        window.location.href = url.toString();
    }
    
    function resetFilters() {
        window.location.href = '{{ route("stores.show", $store->slug) }}';
    }
    
    // Wishlist toggle
    function toggleWishlist(productId) {
        fetch(`/wishlist/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const icon = document.querySelector(`.wishlist-icon-${productId}`);
            if (data.added) {
                icon.setAttribute('fill', 'currentColor');
                icon.classList.add('text-red-500');
            } else {
                icon.setAttribute('fill', 'none');
                icon.classList.remove('text-red-500');
            }
        })
        .catch(error => {
            // Redirect to login if not authenticated
            window.location.href = '/login';
        });
    }

    // Interactive Follow Button
    let following = false;
    function toggleFollowStore(storeId) {
        following = !following;
        const followBtn = document.getElementById('followBtn');
        if (following) {
            followBtn.innerHTML = 'Mengikuti';
            followBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            followBtn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
        } else {
            followBtn.innerHTML = 'Ikuti';
            followBtn.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            followBtn.classList.add('bg-green-600', 'hover:bg-green-700', 'text-white');
        }
    }
</script>
@endpush