import React, { useState, useEffect, useCallback, useRef } from 'react';

const VALID_STOCK_FILTERS = ['ALL', 'BEST_SELLER', 'OUT_OF_STOCK', 'LOW_STOCK', 'IN_STOCK'];
const DEFAULT_CATEGORY_FILTER = '1';
const DEFAULT_STOCK_FILTER = 'ALL';

/**
 * Parse and validate URL query parameters and hash from window.location
 */
export function parseUrlState(validCategoryIds = ['1', '2', '3', '4', 'ALL']) {
    const { pathname, search, hash } = window.location;
    const params = new URLSearchParams(search);

    const rawCategory = params.get('category_filter');
    const rawStock = params.get('stock_filter');
    const rawSearch = params.get('product_search') || '';

    const categoryFilter =
        rawCategory && (validCategoryIds.includes(String(rawCategory)) || /^\d+$/.test(rawCategory))
            ? String(rawCategory)
            : DEFAULT_CATEGORY_FILTER;

    const stockFilter =
        rawStock && VALID_STOCK_FILTERS.includes(rawStock.toUpperCase())
            ? rawStock.toUpperCase()
            : DEFAULT_STOCK_FILTER;

    return {
        pathname,
        categoryFilter,
        stockFilter,
        productSearch: rawSearch,
        hash: hash || '',
    };
}

/**
 * Build a new URL string preserving unrelated query parameters and the current hash
 */
export function buildUpdatedUrl({ categoryFilter, stockFilter, productSearch, hashOverride }) {
    const { pathname, search, hash } = window.location;
    const params = new URLSearchParams(search);

    if (categoryFilter !== undefined) {
        params.set('category_filter', String(categoryFilter));
    }
    if (stockFilter !== undefined) {
        params.set('stock_filter', String(stockFilter));
    }
    if (productSearch !== undefined) {
        if (productSearch.trim() === '') {
            params.delete('product_search');
        } else {
            params.set('product_search', productSearch.trim());
        }
    }

    const queryString = params.toString();
    const targetHash = hashOverride !== undefined ? hashOverride : hash;

    return `${pathname}${queryString ? `?${queryString}` : ''}${targetHash || ''}`;
}

/**
 * Smoothly scroll to a hash target if the element exists in the DOM
 */
export function scrollToHashSection(hashString) {
    if (!hashString || !hashString.startsWith('#')) return false;
    const targetId = hashString.slice(1);
    if (!targetId) return false;

    const element = document.getElementById(targetId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return true;
    }
    return false;
}

export default function AdminProductFilter({
    categories = [
        { id: '1', name: 'Chăm sóc da' },
        { id: '2', name: 'Trang điểm' },
        { id: '3', name: 'Chăm sóc cơ thể' },
        { id: '4', name: 'Nước hoa' },
    ],
    apiEndpoint = '/api/admin/products',
}) {
    const validCategoryIds = [...categories.map((c) => String(c.id)), 'ALL'];

    // C. Initialize React state directly from the current URL
    const [filters, setFilters] = useState(() => parseUrlState(validCategoryIds));
    const [products, setProducts] = useState([]);
    const [totalCount, setTotalCount] = useState(0);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    // Track last fetched query key to prevent duplicate API calls
    const lastFetchedKeyRef = useRef('');
    const abortControllerRef = useRef(null);

    // B. Update URL via History API without reloading the page
    const updateFilters = useCallback(
        (nextPartialFilters, { replace = false, hashOverride } = {}) => {
            setFilters((prev) => {
                const updated = {
                    ...prev,
                    ...nextPartialFilters,
                    hash: hashOverride !== undefined ? hashOverride : window.location.hash,
                };

                const nextUrl = buildUpdatedUrl({
                    categoryFilter: updated.categoryFilter,
                    stockFilter: updated.stockFilter,
                    productSearch: updated.productSearch,
                    hashOverride: updated.hash,
                });

                const currentUrl = `${window.location.pathname}${window.location.search}${window.location.hash}`;
                if (nextUrl !== currentUrl) {
                    if (replace) {
                        window.history.replaceState({ filters: updated }, '', nextUrl);
                    } else {
                        window.history.pushState({ filters: updated }, '', nextUrl);
                    }
                }

                return updated;
            });
        },
        []
    );

    const handleCategoryChange = useCallback(
        (newCategory) => {
            updateFilters({ categoryFilter: String(newCategory) });
        },
        [updateFilters]
    );

    const handleStockChange = useCallback(
        (newStock) => {
            if (!VALID_STOCK_FILTERS.includes(newStock)) return;
            updateFilters({ stockFilter: newStock });
        },
        [updateFilters]
    );

    const handleClearFilters = useCallback(() => {
        updateFilters({
            categoryFilter: DEFAULT_CATEGORY_FILTER,
            stockFilter: DEFAULT_STOCK_FILTER,
            productSearch: '',
        });
    }, [updateFilters]);

    // C. Synchronize state with Browser Back / Forward navigation & hashchange
    useEffect(() => {
        const handlePopState = () => {
            const parsed = parseUrlState(validCategoryIds);
            setFilters(parsed);
            if (parsed.hash) {
                scrollToHashSection(parsed.hash);
            }
        };

        window.addEventListener('popstate', handlePopState);
        window.addEventListener('hashchange', handlePopState);
        return () => {
            window.removeEventListener('popstate', handlePopState);
            window.removeEventListener('hashchange', handlePopState);
        };
    }, [validCategoryIds.join(',')]);

    // D. Fetch filtered products from Laravel API when category_filter or stock_filter changes
    useEffect(() => {
        const requestKey = `${filters.categoryFilter}|${filters.stockFilter}|${filters.productSearch}`;
        if (lastFetchedKeyRef.current === requestKey) {
            return;
        }
        lastFetchedKeyRef.current = requestKey;

        if (abortControllerRef.current) {
            abortControllerRef.current.abort();
        }
        const controller = new AbortController();
        abortControllerRef.current = controller;

        async function fetchFilteredProducts() {
            setLoading(true);
            setError(null);

            try {
                const query = new URLSearchParams({
                    category_filter: filters.categoryFilter,
                    stock_filter: filters.stockFilter,
                });
                if (filters.productSearch) {
                    query.set('product_search', filters.productSearch);
                }

                const response = await fetch(`${apiEndpoint}?${query.toString()}`, {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                });

                if (!response.ok) {
                    const errBody = await response.json().catch(() => ({}));
                    throw new Error(errBody.message || `HTTP Error ${response.status}`);
                }

                const result = await response.json();
                setProducts(Array.isArray(result.data) ? result.data : []);
                setTotalCount(Number(result.total_count || 0));
            } catch (err) {
                if (err.name === 'AbortError') return;
                setError(err.message || 'Không thể tải danh sách sản phẩm.');
                lastFetchedKeyRef.current = '';
            } finally {
                if (!controller.signal.aborted) {
                    setLoading(false);
                }
            }
        }

        fetchFilteredProducts();

        return () => {
            controller.abort();
        };
    }, [filters.categoryFilter, filters.stockFilter, filters.productSearch, apiEndpoint]);

    // E. Hash Navigation on initial load and when hash changes
    useEffect(() => {
        if (!filters.hash) return;
        const timer = setTimeout(() => {
            scrollToHashSection(filters.hash);
        }, 100);
        return () => clearTimeout(timer);
    }, [filters.hash]);

    return (
        <div className="space-y-6">
            {/* Filter Controls */}
            <div className="bg-white rounded-3xl border border-sage-100 p-6 shadow-xs space-y-4">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span className="text-xs font-bold uppercase tracking-widest text-sage-500">
                            Quản Lý Kho Hàng (URL Synced)
                        </span>
                        <h3 className="font-serif text-xl font-bold text-stone-800">
                            Danh Sách Sản Phẩm ({products.length} / {totalCount})
                        </h3>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <a
                            href="#section-coupons"
                            onClick={(e) => {
                                e.preventDefault();
                                updateFilters({}, { hashOverride: '#section-coupons' });
                                scrollToHashSection('#section-coupons');
                            }}
                            className="text-xs font-semibold px-3 py-1.5 rounded-xl bg-sage-50 text-sage-700 border border-sage-200 hover:bg-sage-100 transition"
                        >
                            🎟️ Tới mục Voucher (#section-coupons)
                        </a>
                        <button
                            type="button"
                            onClick={handleClearFilters}
                            className="text-xs font-semibold px-3 py-1.5 rounded-xl bg-stone-100 text-stone-700 hover:bg-stone-200 transition"
                        >
                            Đặt lại bộ lọc
                        </button>
                    </div>
                </div>

                {/* Category & Stock Filter Selectors */}
                <div className="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-stone-100">
                    <div className="flex items-center gap-2">
                        <label htmlFor="react-category-filter" className="text-xs font-semibold text-stone-600">
                            Danh mục:
                        </label>
                        <select
                            id="react-category-filter"
                            value={filters.categoryFilter}
                            onChange={(e) => handleCategoryChange(e.target.value)}
                            className="px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50"
                        >
                            <option value="ALL">Mọi danh mục</option>
                            {categories.map((cat) => (
                                <option key={cat.id} value={String(cat.id)}>
                                    {cat.name}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div className="flex flex-wrap gap-1.5 text-xs">
                        {[
                            { key: 'ALL', label: 'Tất cả' },
                            { key: 'BEST_SELLER', label: '★ Bán chạy' },
                            { key: 'OUT_OF_STOCK', label: '🔴 Hết hàng' },
                            { key: 'LOW_STOCK', label: '🟠 Sắp hết' },
                            { key: 'IN_STOCK', label: '🟢 Còn nhiều' },
                        ].map((tab) => (
                            <button
                                key={tab.key}
                                type="button"
                                onClick={() => handleStockChange(tab.key)}
                                className={`px-3 py-1.5 rounded-xl font-semibold transition ${
                                    filters.stockFilter === tab.key
                                        ? 'bg-stone-900 text-white'
                                        : 'bg-stone-100 text-stone-600 hover:bg-stone-200'
                                }`}
                            >
                                {tab.label}
                            </button>
                        ))}
                    </div>
                </div>
            </div>

            {/* Loading, Error, Empty, and Data States */}
            {loading && (
                <div className="p-8 text-center text-xs font-semibold text-sage-600 bg-white rounded-3xl border border-stone-200/80">
                    ⏳ Đang tải dữ liệu sản phẩm theo bộ lọc...
                </div>
            )}

            {!loading && error && (
                <div className="p-6 text-center text-xs font-semibold text-red-600 bg-red-50 rounded-3xl border border-red-200">
                    ⚠ Lỗi tải dữ liệu: {error}
                </div>
            )}

            {!loading && !error && products.length === 0 && (
                <div className="p-10 text-center text-xs text-stone-400 bg-white rounded-3xl border border-stone-200/80">
                    Không có sản phẩm nào khớp với bộ lọc (category_filter={filters.categoryFilter}, stock_filter={filters.stockFilter}).
                </div>
            )}

            {!loading && !error && products.length > 0 && (
                <div className="space-y-3">
                    {products.map((product) => (
                        <div
                            key={product.id}
                            className="flex items-center justify-between gap-4 p-3.5 rounded-2xl bg-white border border-stone-200/80 hover:border-sage-200 transition"
                        >
                            <div className="flex items-center gap-3.5 min-w-0">
                                <img
                                    src={product.image}
                                    alt={product.name}
                                    className="w-14 h-14 rounded-2xl object-cover bg-white border border-stone-200 flex-shrink-0"
                                />
                                <div className="min-w-0">
                                    <div className="flex items-center gap-1.5">
                                        <span className="font-mono font-bold text-[10px] bg-stone-900 text-white px-2 py-0.5 rounded-md">
                                            {product.sku}
                                        </span>
                                        <span className="text-[10px] font-bold uppercase text-sage-500">
                                            {product.brand_name}
                                        </span>
                                        <span className="text-[10px] text-stone-400">
                                            • {product.category_name}
                                        </span>
                                    </div>
                                    <p className="text-sm font-bold text-stone-800 truncate">{product.name}</p>
                                    <p className="text-xs font-bold text-sage-600">
                                        {Number(product.final_price).toLocaleString('vi-VN')}₫ • Kho: {product.stock} SP
                                    </p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
