--
-- PostgreSQL database dump
--


-- Dumped from database version 16.15
-- Dumped by pg_dump version 16.15

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: brands; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.brands (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    logo character varying(255) NOT NULL,
    origin character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: brands_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.brands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: brands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.brands_id_seq OWNED BY public.brands.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cart_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cart_items (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    product_id bigint NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: cart_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cart_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: cart_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cart_items_id_seq OWNED BY public.cart_items.id;


--
-- Name: categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    image character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: coupons; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.coupons (
    id bigint NOT NULL,
    code character varying(255) NOT NULL,
    discount_percent integer NOT NULL,
    description character varying(255),
    min_order_amount bigint DEFAULT '0'::bigint NOT NULL,
    expires_at date NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: coupons_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.coupons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: coupons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.coupons_id_seq OWNED BY public.coupons.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: order_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.order_items (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    product_id bigint NOT NULL,
    quantity integer NOT NULL,
    price bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: order_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.order_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: order_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.order_items_id_seq OWNED BY public.order_items.id;


--
-- Name: orders; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.orders (
    id bigint NOT NULL,
    customer_name character varying(255) NOT NULL,
    phone character varying(255) NOT NULL,
    address text NOT NULL,
    note text,
    payment_method character varying(255) DEFAULT 'COD'::character varying NOT NULL,
    total_amount bigint NOT NULL,
    status character varying(255) DEFAULT 'CONFIRMED'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    user_id bigint,
    coupon_code character varying(255),
    discount_amount bigint DEFAULT '0'::bigint NOT NULL
);


--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: products; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.products (
    id bigint NOT NULL,
    sku character varying(255) NOT NULL,
    category_id bigint NOT NULL,
    brand_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    price bigint NOT NULL,
    sale_price bigint,
    stock integer DEFAULT 20 NOT NULL,
    images json NOT NULL,
    description text NOT NULL,
    ingredients text NOT NULL,
    skin_type character varying(255) DEFAULT 'Mọi loại da'::character varying NOT NULL,
    volume character varying(255) DEFAULT '30ml'::character varying NOT NULL,
    is_best_seller boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: user_addresses; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_addresses (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    label character varying(255) DEFAULT 'Nhà riêng'::character varying NOT NULL,
    recipient_name character varying(255),
    recipient_phone character varying(30),
    street_address character varying(300),
    ward character varying(150),
    district character varying(150),
    province character varying(150),
    address character varying(500) NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_addresses_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_addresses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_addresses_id_seq OWNED BY public.user_addresses.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    username character varying(255),
    phone character varying(255),
    address character varying(255),
    is_admin boolean DEFAULT false NOT NULL,
    role character varying(255) DEFAULT 'customer'::character varying NOT NULL
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: wishlists; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wishlists (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    product_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: wishlists_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wishlists_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wishlists_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wishlists_id_seq OWNED BY public.wishlists.id;


--
-- Name: brands id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.brands ALTER COLUMN id SET DEFAULT nextval('public.brands_id_seq'::regclass);


--
-- Name: cart_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cart_items ALTER COLUMN id SET DEFAULT nextval('public.cart_items_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: coupons id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.coupons ALTER COLUMN id SET DEFAULT nextval('public.coupons_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: order_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_items ALTER COLUMN id SET DEFAULT nextval('public.order_items_id_seq'::regclass);


--
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: user_addresses id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_addresses ALTER COLUMN id SET DEFAULT nextval('public.user_addresses_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: wishlists id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wishlists ALTER COLUMN id SET DEFAULT nextval('public.wishlists_id_seq'::regclass);


--
-- Data for Name: brands; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.brands (id, name, logo, origin, created_at, updated_at) FROM stdin;
1	La Roche-Posay	https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80	Pháp	2026-11-04 15:52:55	2026-11-04 15:52:55
2	Sulwhasoo	https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=400&q=80	Hàn Quốc	2026-11-04 15:52:55	2026-11-04 15:52:55
3	Shiseido	https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=400&q=80	Nhật Bản	2026-11-04 15:52:55	2026-11-04 15:52:55
4	Lancôme	https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=400&q=80	Pháp	2026-11-04 15:52:55	2026-11-04 15:52:55
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: cart_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cart_items (id, user_id, product_id, quantity, created_at, updated_at) FROM stdin;
34	3	45	1	2026-11-11 22:32:56	2026-11-11 22:32:56
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categories (id, name, slug, image, created_at, updated_at) FROM stdin;
1	Chăm sóc da	cham-soc-da	https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80	2026-11-04 15:52:55	2026-11-04 15:52:55
2	Trang điểm	trang-diem	https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=800&q=80	2026-11-04 15:52:55	2026-11-04 15:52:55
3	Chăm sóc cơ thể	cham-soc-co-the	https://images.unsplash.com/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=800&q=80	2026-11-04 15:52:55	2026-11-04 15:52:55
4	Nước hoa	nuoc-hoa	https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=800&q=80	2026-11-04 15:52:55	2026-11-04 15:52:55
\.


--
-- Data for Name: coupons; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.coupons (id, code, discount_percent, description, min_order_amount, expires_at, is_active, created_at, updated_at) FROM stdin;
1	LUMINA20	20	Giảm 20% toàn bộ dòng Serum & Kem dưỡng chăm sóc da cao cấp	300000	2027-05-04	t	2026-11-04 15:52:55	2026-11-04 15:52:55
2	FLASHSALE30	30	Siêu ưu đãi Flash Sale giảm sâu 30% cho thành viên thân thiết	800000	2027-02-04	t	2026-11-04 15:52:55	2026-11-04 15:52:55
3	SKINCARE15	15	Giảm 15% cho các sản phẩm phục hồi da nhạy cảm B5 & Ceramide	250000	2027-05-04	t	2026-11-04 15:52:55	2026-11-04 15:52:55
4	NEWMEMBER10	10	Quà tặng chào mừng khách hàng mới giảm ngay 10% mọi đơn hàng	0	2027-11-04	t	2026-11-04 15:52:55	2026-11-04 15:52:55
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_11_03_000001_create_cosmetics_tables	1
5	2026_11_03_000002_add_admin_and_coupons_tables	1
\.


--
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.order_items (id, order_id, product_id, quantity, price, created_at, updated_at) FROM stdin;
1	1	1	2	345000	2026-11-04 15:52:55	2026-11-04 15:52:55
2	1	4	1	550000	2026-11-04 15:52:55	2026-11-04 15:52:55
3	2	2	1	750000	2026-11-04 15:52:55	2026-11-04 15:52:55
4	2	7	1	1150000	2026-11-04 15:52:55	2026-11-04 15:52:55
5	3	12	1	2790000	2026-11-04 15:52:55	2026-11-04 15:52:55
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.orders (id, customer_name, phone, address, note, payment_method, total_amount, status, created_at, updated_at, user_id, coupon_code, discount_amount) FROM stdin;
1	Tuyền Thanh Thanh	0909397979	215 Lê Thánh Tôn, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh	Giao giờ hành chính, gọi trước khi giao 15 phút	COD	992000	COMPLETED	2026-11-04 15:52:55	2026-11-04 15:52:55	3	LUMINA20	248000
2	Tuyền Thanh Thanh	0909397979	215 Lê Thánh Tôn, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh	Tặng kèm mẫu thử kem chống nắng La Roche-Posay	BANK_TRANSFER	1615000	SHIPPING	2026-11-04 15:52:55	2026-11-04 15:52:55	3	SKINCARE15	285000
3	Lê Hoàng Bảo Trâm	0918888999	254 Cầu Giấy, Quận Cầu Giấy, Hà Nội	Đóng gói hộp quà tặng kèm thiệp sinh nhật	COD	2790000	PENDING	2026-11-04 15:52:55	2026-11-04 15:52:55	\N	\N	0
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.products (id, sku, category_id, brand_id, name, slug, price, sale_price, stock, images, description, ingredients, skin_type, volume, is_best_seller, created_at, updated_at) FROM stdin;
1	LMN-SP001	1	1	Tinh Chất Sáng Da Mờ Thâm Niacinamide 10% + Zinc 1%	tinh-chat-sang-da-mo-tham-niacinamide-10-zinc-1	420000	345000	20	["https:\\/\\/images.unsplash.com\\/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80"]	Tinh chất cô đặc chứa 10% Niacinamide tinh khiết kết hợp 1% Zinc PCA giúp kiểm soát dầu thừa tối ưu, thu nhỏ lỗ chân lông, làm dịu nốt mụn sưng đỏ và cải thiện sắc tố da sáng mịn đều màu sau 4 tuần sử dụng.	Aqua (Water), Niacinamide (10%), Pentylene Glycol, Zinc PCA (1%), Dimethyl Isosorbide, Tamarindus Indica Seed Gum, Xanthan Gum, Isoceteth-20, Ethoxydiglycol, Phenoxyethanol, Chlorphenesin.	Da dầu	30ml	t	2026-11-04 15:52:55	2026-11-04 15:52:55
2	LMN-SP002	1	1	Dung Dịch Loại Bỏ Tế Bào Chết BHA 2% Salicylic Acid	dung-dich-loai-bo-te-bao-chet-bha-2-salicylic-acid	890000	750000	20	["https:\\/\\/images.unsplash.com\\/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80"]	Dung dịch tẩy tế bào chết hóa học chứa 2% BHA (Salicylic Acid) thẩm thấu sâu vào lỗ chân lông giúp đẩy mụn ẩn, làm sạch sợi bã nhờn, ngăn ngừa mụn đầu đen và tái tạo bề mặt da láng mịn.	Water, Methylpropanediol, Butylene Glycol, Salicylic Acid (BHA 2%), Polysorbate 20, Camellia Oleifera (Green Tea) Leaf Extract, Sodium Hydroxide, Tetrasodium EDTA.	Da dầu	118ml	t	2026-11-04 15:52:55	2026-11-04 15:52:55
3	LMN-SP003	1	3	Kem Dưỡng Trẻ Hóa Da Ban Đêm Retinol 0.5% Tinh Khiết	kem-duong-tre-hoa-da-ban-dem-retinol-0-5-tinh-khiet	1450000	1190000	20	["https:\\/\\/images.unsplash.com\\/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=900&q=80"]	Kem dưỡng chống lão hóa chuyên sâu với công thức Retinol 0.5% bọc vi nang kết hợp Peptide và Ceramide giúp kích thích tăng sinh Collagen, làm mờ nếp nhăn li ti và nâng cơ săn chắc mà không gây bong tróc.	Aqua, Glycerin, Caprylic/Capric Triglyceride, Retinol (0.5%), Ceramide NP, Niacinamide, Hyaluronic Acid, Tocopherol (Vitamin E), Panthenol, Squalane.	Mọi loại da	50ml	t	2026-11-04 15:52:55	2026-11-04 15:52:55
4	LMN-SP004	1	1	Serum Cấp Ẩm Đa Tầng Hyaluronic Acid 2% + Vitamin B5	serum-cap-am-da-tang-hyaluronic-acid-2-vitamin-b5	680000	550000	20	["https:\\/\\/images.unsplash.com\\/photo-1617897903246-719242758050?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80"]	Serum cấp nước chuyên sâu với phân tử Hyaluronic Acid đa kích thước kết hợp Pro-Vitamin B5 giúp phục hồi màng bảo vệ da, làm dịu tình trạng khô căng bong tróc và mang lại làn da căng bóng ngậm nước.	Aqua (Water), Sodium Hyaluronate, Sodium Hyaluronate Crosspolymer, Panthenol (Vitamin B5), Ahnfeltia Concinna Extract, Glycerin, Pentylene Glycol, Propanediol.	Da khô	30ml	t	2026-11-04 15:52:55	2026-11-04 15:52:55
5	LMN-SP005	1	2	Kem Dưỡng Phục Hồi Nhân Sâm Cô Đặc Sulwhasoo Concentrated Ginseng	kem-duong-phuc-hoi-nhan-sam-co-dac-sulwhasoo	2650000	2290000	20	["https:\\/\\/images.unsplash.com\\/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80"]	Tuyệt tác dưỡng da hoàng cung Hàn Quốc chiết xuất từ tinh chất Nhân Sâm cô đặc gấp 6000 lần kết hợp Squalane và Hyaluronic Acid nuôi dưỡng làn da khô lão hóa trở nên hồng hào, săn chắc từ sâu bên trong.	Panax Ginseng Root Water, Squalane, Butylene Glycol, Glycerin, Hyaluronic Acid, Niacinamide, Shea Butter, Adenosine, Tocopherol, Honey Extract.	Da khô	60ml	f	2026-11-04 15:52:55	2026-11-04 15:52:55
6	LMN-SP006	1	1	Kem Dưỡng Làm Dịu Phục Hồi Da Nhạy Cảm Cicaplast Baume B5+	kem-duong-lam-diu-phuc-hoi-da-nhay-cam-cicaplast-baume-b5	495000	415000	20	["https:\\/\\/images.unsplash.com\\/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80"]	Kem dưỡng quốc dân dành riêng cho làn da nhạy cảm, kích ứng hoặc sau treatment. Chứa 5% Panthenol (Vitamin B5) và Madecassoside chiết xuất rau má giúp làm dịu vết đỏ tức thì và tái tạo hàng rào ẩm tự nhiên.	Aqua, Hydrogenated Polyisobutene, Dimethicone, Glycerin, Butyrospermum Parkii Butter, Panthenol (5%), Madecassoside, Tribioma, Zinc Gluconate, Silica.	Da nhạy cảm	100ml	t	2026-11-04 15:52:55	2026-11-04 15:52:55
7	LMN-SP007	2	2	Phấn Nước Trang Điểm Căng Bóng Sulwhasoo Perfecting Cushion	phan-nuoc-trang-diem-cang-bong-sulwhasoo-perfecting-cushion	1350000	1150000	20	["https:\\/\\/images.unsplash.com\\/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80"]	Phấn nước cao cấp kết hợp dưỡng chất skincare giúp che phủ hoàn hảo khuyết điểm suốt 24 giờ mà vẫn giữ lớp nền mỏng nhẹ, căng bóng tự nhiên chuẩn Hàn Quốc cùng chỉ số chống nắng SPF50+ PA+++.	Water, Titanium Dioxide, Cyclopentasiloxane, Ethylhexyl Methoxycinnamate, Niacinamide, Hyaluronic Acid, Schizophyllum Commune Mycelium Ferment Filtrate, Adenosine.	Mọi loại da	15g x 2	t	2026-11-04 15:52:55	2026-11-04 15:52:55
8	LMN-SP008	2	4	Son Thỏi Lì Mịn Môi Lancôme L'Absolu Rouge Drama Matte	son-thoi-li-min-moi-lancome-labsolu-rouge-drama-matte	980000	860000	20	["https:\\/\\/images.unsplash.com\\/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80"]	Son thỏi lì cao cấp sắc hồng đất thời thượng, bổ sung chiết xuất hoa hồng Grand Rose và Hyaluronic Acid giúp đôi môi lên màu chuẩn sắc ngay từ lần chạm đầu tiên, mềm mượt không gây khô môi.	Dimethicone, Bis-Diglyceryl Polyacyladipate-2, Hydrogenated Polyisobutene, Rosa Centifolia Flower Extract, Hyaluronic Acid, Simmondsia Chinensis (Jojoba) Seed Oil, Tocopherol.	Mọi loại da	3.4g	f	2026-11-04 15:52:55	2026-11-04 15:52:55
9	LMN-SP009	2	3	Kem Nền Mỏng Nhẹ Bắt Sáng Shiseido Synchro Skin Radiant Lifting	kem-nen-mong-nhe-bat-sang-shiseido-synchro-skin	1290000	1090000	20	["https:\\/\\/images.unsplash.com\\/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80"]	Ứng dụng công nghệ điều chỉnh ánh sáng thông minh từ Nhật Bản, giúp che phủ nếp nhăn và vùng da không đều màu, đồng thời khóa ẩm suốt 24 giờ cho làn da khô luôn rạng rỡ.	Water, Dimethicone, Titanium Dioxide, Glycerin, Niacinamide, Citrus Unshiu Peel Extract, Fagus Sylvatica Bud Extract, Sodium Acetylated Hyaluronate.	Da khô	30ml	f	2026-11-04 15:52:55	2026-11-04 15:52:55
10	LMN-SP010	3	1	Sữa Dưỡng Thể Trắng Sáng Niacinamide & Bơ Hạt Mỡ Lipikar Lait	sua-duong-the-trang-sang-niacinamide-bo-hat-mo-lipikar	560000	465000	20	["https:\\/\\/images.unsplash.com\\/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80"]	Sữa dưỡng thể giàu Niacinamide tinh khiết và 10% Bơ Hạt Mỡ (Shea Butter) giúp nuôi dưỡng làn da cơ thể mềm mại, dưỡng sáng đều màu và làm dịu cảm giác khô ngứa trên da nhạy cảm suốt 48 giờ.	Aqua, Butyrospermum Parkii Butter (10%), Glycerin, Sorbitan Stearate, Paraffinum Liquidum, Niacinamide (4%), Brassica Campestris Oleifera Oil, Panthenol.	Da nhạy cảm	400ml	f	2026-11-04 15:52:55	2026-11-04 15:52:55
11	LMN-SP011	3	3	Tẩy Tế Bào Chết Toàn Thân Muối Hồng & BHA Sáng Mịn Cơ Thể	tay-te-bao-chet-toan-than-muoi-hong-bha-sang-min	520000	430000	20	["https:\\/\\/images.unsplash.com\\/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=900&q=80"]	Sự kết hợp hoàn hảo giữa muối hồng Himalaya mịn nhuyễn, 0.5% BHA (Salicylic Acid) và tinh dầu hoa trà Nhật Bản giúp làm sạch sâu lỗ chân lông, hỗ trợ giảm viêm nang lông và mang lại làn da body mướt mịn.	Maris Sal (Himalayan Pink Salt), Glycerin, Cocamidopropyl Betaine, Salicylic Acid (BHA), Camellia Japonica Seed Oil, Niacinamide, Tocopheryl Acetate, Rosa Damascena Flower Oil.	Da dầu	250g	f	2026-11-04 15:52:55	2026-11-04 15:52:55
12	LMN-SP012	4	4	Nước Hoa Nữ Lancôme La Vie Est Belle Rose Extraordinaire EDP	nuoc-hoa-nu-lancome-la-vie-est-belle-rose-edp	3250000	2790000	20	["https:\\/\\/images.unsplash.com\\/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=900&q=80"]	Biểu tượng hương thơm ngọt ngào, kiêu sa từ bộ ba chiết xuất hoa hồng Damask quý giá quyện cùng hương cam Bergamot thanh mát, hoa Diên Vĩ và xạ hương gỗ ấm áp, lưu hương bền bỉ đến 12 giờ.	Alcohol, Parfum / Fragrance, Aqua / Water, Rosa Damascena Flower Water, Rosa Damascena Flower Oil, Iris Pallida Root Extract, Linalool, Limonene, Citronellol.	Mọi loại da	100ml	t	2026-11-04 15:52:55	2026-11-04 15:52:55
34	LMN-SP013	1	1	Sữa Rửa Mặt Tạo Bọt Dịu Nhẹ La Roche-Posay Effaclar Purifying Gel	sua-rua-mat-tao-bot-diu-nhe-la-roche-posay-effaclar-gel	385000	315000	50	["https:\\/\\/images.unsplash.com\\/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80"]	Gel rửa mặt tạo bọt không chứa xà phòng với nước khoáng La Roche-Posay và Zinc Pidolate giúp làm sạch sâu bụi bẩn, bã nhờn dư thừa mà vẫn giữ độ pH 5.5 dịu nhẹ cho làn da dầu mụn.	Aqua, Sodium Laureth Sulfate, PEG-8, Coco-Betaine, Hexylene Glycol, Sodium Chloride, Zinc PCA, Sodium Hydroxide, Citric Acid.	Da dầu	200ml	t	2026-11-08 22:21:22	2026-11-08 22:21:22
35	LMN-SP014	1	1	Nước Tẩy Trang Làm Sạch Sâu La Roche-Posay Micellar Water Sensitive	nuoc-tay-trang-lam-sach-sau-la-roche-posay-micellar-water	430000	355000	60	["https:\\/\\/images.unsplash.com\\/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1617897903246-719242758050?auto=format&fit=crop&w=900&q=80"]	Công nghệ Glyco-Micellar cuốn sạch 99% lớp trang điểm, kem chống nắng và bụi mịn PM2.5 chỉ sau một lượt bông tẩy trang, bổ sung Glycerin cấp ẩm tức thì cho da nhạy cảm.	Aqua / Water, PEG-7 Caprylic/Capric Glycerides, Poloxamer 124, Poloxamer 184, PEG-6 Caprylic/Capric Glycerides, Glycerin, Polysorbate 80, Disodium EDTA.	Da nhạy cảm	400ml	t	2026-11-08 22:21:22	2026-11-08 22:21:22
36	LMN-SP015	1	2	Nước Hoa Hồng Cân Bằng Ẩm Sulwhasoo Essential Comfort Balancing Water	nuoc-hoa-hong-can-bang-am-sulwhasoo-essential-comfort	480000	395000	45	["https:\\/\\/images.unsplash.com\\/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80"]	Nước cân bằng kết cấu dạng gel mỏng nhẹ chiết xuất từ hợp chất thảo mộc JAUM Moisturizing Complex™ giúp làm dịu da khô ráp và cấp nước tức thì sau bước rửa mặt.	Water, Alcohol Denat., Betaine, Butylene Glycol, Glyceryl Polymethacrylate, Camellia Sinensis Leaf Extract, Liriope Platyphylla Root Extract, Panthenol.	Da khô	75ml	f	2026-11-08 22:21:22	2026-11-08 22:21:22
37	LMN-SP016	1	1	Xịt Khoáng Làm Dịu & Cấp Nước Tức Thì La Roche-Posay Thermal Water	xit-khoang-lam-diu-cap-nuoc-la-roche-posay-thermal-water	295000	235000	80	["https:\\/\\/images.unsplash.com\\/photo-1617897903246-719242758050?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80"]	Nguồn khoáng thiên nhiên giàu Selenium và nguyên tố vi lượng từ Pháp giúp làm dịu vết ửng đỏ, giảm kích ứng do nắng nóng và khóa ẩm nhẹ nhàng suốt cả ngày.	Aqua / Thermal Spring Water (100% Pure La Roche-Posay Thermal Spring Water Rich in Selenium).	Mọi loại da	150ml	f	2026-11-08 22:21:22	2026-11-08 22:21:22
38	LMN-SP017	1	3	Kem Chống Nắng Kiềm Dầu Nâng Tông Shiseido Urban Environment SPF50+	kem-chong-nang-kiem-dau-nang-tong-shiseido-urban-spf50	490000	425000	55	["https:\\/\\/images.unsplash.com\\/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80"]	Sữa chống nắng vật lý lai hóa học ứng dụng công nghệ SuperVeil-UV 360™ bảo vệ da toàn diện trước tia UVA/UVB, kiềm dầu suốt 8 giờ và nâng tông hồng hào tự nhiên.	Water, Zinc Oxide, Ethylhexyl Methoxycinnamate, Cyclopentasiloxane, Glycerin, Sodium Hyaluronate, Scutellaria Baicalensis Root Extract.	Da dầu	30ml	t	2026-11-08 22:21:22	2026-11-08 22:21:22
39	LMN-SP018	1	1	Gel Chấm Mụn Giảm Sưng Nhanh BHA & Niacinamide Effaclar Duo+M Mini	gel-cham-mun-giam-sung-bha-niacinamide-effaclar-duo-mini	340000	275000	70	["https:\\/\\/images.unsplash.com\\/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80"]	Gel chấm mụn chuyên biệt với hoạt chất Phylobioma kết hợp Niacinamide, LHA và Salicylic Acid giúp gom cồi mụn nhanh trong 8 giờ và ngăn ngừa vết thâm sau mụn.	Aqua, Glycerin, Dimethicone, Isocetyl Stearate, Niacinamide, Salicylic Acid, Capryloyl Salicylic Acid, Zinc PCA, Piroctone Olamine.	Da dầu	15ml	f	2026-11-08 22:21:22	2026-11-08 22:21:22
40	LMN-SP019	2	4	Son Dưỡng Môi Có Màu Căng Mọng Lancôme L'Absolu Mademoiselle Balm	son-duong-moi-co-mau-cang-mong-lancome-mademoiselle-balm	460000	380000	40	["https:\\/\\/images.unsplash.com\\/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80"]	Son dưỡng có màu giàu tinh dầu hoa hồng Pháp và tinh chất bạc hà mát lạnh giúp nuôi dưỡng đôi môi mềm mọng, ửng hồng tươi tắn suốt cả ngày dài.	Diisostearyl Malate, Hydrogenated Polyisobutene, Phenyl Trimethicone, Rosa Canina Fruit Oil, Macadamia Integrifolia Seed Oil, Tocopherol.	Mọi loại da	3.2g	f	2026-11-08 22:21:22	2026-11-08 22:21:22
41	LMN-SP020	2	2	Mặt Nạ Ngủ Môi Nhân Sâm Mềm Mượt Sulwhasoo Essential Lip Mask	mat-na-ngu-moi-nhan-sam-mem-muot-sulwhasoo-lip-mask	390000	320000	50	["https:\\/\\/images.unsplash.com\\/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80"]	Mặt nạ ủ môi ban đêm giàu chiết xuất Nhân Sâm Đỏ và bơ Murumuru giúp làm mềm lớp tế bào chết khô nứt, trả lại bờ môi căng mọng và hồng hào mỗi sáng thức dậy.	Hydrogenated Polyisobutene, Phytosteryl/Isostearyl/Cetyl/Stearyl/Behenyl Dimer Dilinoleate, Astrocaryum Murumuru Seed Butter, Panax Ginseng Seed Oil.	Da khô	10g	f	2026-11-08 22:21:22	2026-11-08 22:21:22
42	LMN-SP021	2	3	Chì Kẻ Mày Tự Nhiên Lâu Trôi Shiseido Brow InkTrio Chống Nước	chi-ke-may-tu-nhien-lau-troi-shiseido-brow-inktrio	350000	285000	65	["https:\\/\\/images.unsplash.com\\/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=900&q=80"]	Chì kẻ chân mày 3 trong 1 tích hợp đầu chì mảnh định hình sợi, đầu phấn tán tự nhiên và cọ chải định hình lông mày sắc nét, chống trôi mồ hôi suốt 24 giờ.	Behenic Acid, Rhus Succedanea Fruit Wax, Triethylhexanoin, Sucrose Tetrastearate Triacetate, Tocopherol, Iron Oxides.	Mọi loại da	0.3g	f	2026-11-08 22:21:22	2026-11-08 22:21:22
43	LMN-SP022	3	1	Kem Dưỡng Da Tay & Móng Bơ Hạt Mỡ La Roche-Posay Cicaplast Mains	kem-duong-da-tay-bo-hat-mo-la-roche-posay-cicaplast-mains	260000	195000	90	["https:\\/\\/images.unsplash.com\\/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80"]	Kem dưỡng phục hồi da tay khô nứt với 4% Niacinamide và 30% Glycerin tạo màng chắn vô hình bảo vệ đôi bàn tay mềm mịn, thấm nhanh không nhờn rít.	Aqua / Water, Glycerin (30%), Butylene Glycol, Cetearyl Alcohol, Niacinamide (4%), Glyceryl Stearate, Butyrospermum Parkii Butter / Shea Butter.	Da nhạy cảm	50ml	f	2026-11-08 22:21:22	2026-11-08 22:21:22
44	LMN-SP023	3	3	Sữa Tắm Tinh Dầu Hoa Trà Dưỡng Ẩm Shiseido Kuyura Body Care Wash	sua-tam-tinh-dau-hoa-tra-duong-am-shiseido-kuyura	320000	255000	75	["https:\\/\\/images.unsplash.com\\/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80"]	Sữa tắm tạo bọt tơ tằm giàu tinh dầu Hoa Trà Tsubaki Nhật Bản giúp làm sạch dịu nhẹ, lưu lại hương hoa thanh khiết và làn da cơ thể ẩm mịn suốt ngày dài.	Water, Lauric Acid, Myristic Acid, Potassium Hydroxide, Glycol Distearate, Camellia Japonica Seed Oil, Sodium Hyaluronate, Glycerin.	Mọi loại da	350ml	f	2026-11-08 22:21:22	2026-11-08 22:21:22
45	LMN-SP024	4	4	Xịt Thơm Toàn Thân Hương Hoa Hồng Lancôme Idôle Hair & Body Mist	xit-thom-toan-than-huong-hoa-hong-lancome-idole-body-mist	495000	445000	60	["https:\\/\\/images.unsplash.com\\/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=900&q=80","https:\\/\\/images.unsplash.com\\/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=80"]	Xịt thơm dưỡng ẩm cho tóc và cơ thể mang nốt hương hoa hồng Isparta thanh tao kết hợp hoa nhài trắng và xạ hương sạch sẽ, tươi mát suốt 6 giờ.	Alcohol, Aqua / Water, Parfum / Fragrance, Panthenol, Rosa Damascena Flower Water, Jasminum Grandiflorum Flower Extract, Linalool, Limonene.	Mọi loại da	50ml	t	2026-11-08 22:21:22	2026-11-08 22:21:22
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: user_addresses; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_addresses (id, user_id, label, recipient_name, recipient_phone, street_address, ward, district, province, address, is_default, created_at, updated_at) FROM stdin;
1	3	Nhà riêng	Tuyền Thanh Thanh	0909397979	215 Lê Thánh Tôn	Phường Bến Thành	Quận 1	TP. Hồ Chí Minh	215 Lê Thánh Tôn, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh	t	2026-11-11 22:57:33	2026-11-11 22:57:33
2	3	Văn phòng Công ty	Tuyền Thanh Thanh	0909397979	Tòa nhà Bitexco, Số 2 Hải Triều	Phường Bến Nghé	Quận 1	TP. Hồ Chí Minh	Tòa nhà Bitexco, Số 2 Hải Triều, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh	f	2026-11-11 22:57:33	2026-11-11 22:57:33
3	3	Địa chỉ người thân	Tuyền Thanh Thanh	0909397979	Số 28 Phạm Văn Chiêu	Phường 14	Quận Gò Vấp	TP. Hồ Chí Minh	Số 28 Phạm Văn Chiêu, Phường 14, Quận Gò Vấp, TP. Hồ Chí Minh	f	2026-11-11 22:57:33	2026-11-11 22:57:33
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, username, phone, address, is_admin, role) FROM stdin;
2	Nguyễn Phong (Shop Owner)	phongdev3979@gmail.com	\N	$2y$12$GIhiPmgXXvENn.A0wrO3G.wcnxnv4b7uBLsdrztCmfiyISs8.KvcS	\N	2026-11-04 15:52:55	2026-11-04 15:52:55	nguyenphong2705	0909270599	Showroom Lumina Beauty Quận 1, TP.HCM	t	admin
3	Tuyền Thanh Thanh	tuyenthanhthanh3979@gmail.com	\N	$2y$12$Z5EZm9N1jat.OpiIQ1JyiOPpVk/3MOOpmUZ5Q20h9EHURq8CoaUOq	bf8E8NucfoR8EUuZbEAOPOOTE9BoqVrnF9BWXZgQes8DmenEy3VxfT1umNVY	2026-11-04 15:52:55	2026-11-04 15:52:55	tuyenthanhthanh3979	0909397979	215 Lê Thánh Tôn, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh	f	customer
1	Chủ Shop Lumina Beauty	admin@luminabeauty.vn	\N	$2y$12$2Jo1VdWxrmGvpy29kSmuG.HSE.N5NIVf.a4Z9uCZsx6K1CiPv/vJC	mogelrec3uGwb6oblRSw4uqlqJyN9PQ4dJfbsoitoaJBgsVy2rIYaqbxaqRC	2026-11-04 15:52:54	2026-11-04 15:52:54	admin	0901000888	Showroom 128 Nguyễn Huệ, Quận 1, TP.HCM	t	admin
\.


--
-- Data for Name: wishlists; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.wishlists (id, user_id, product_id, created_at, updated_at) FROM stdin;
\.


--
-- Name: brands_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.brands_id_seq', 33, true);


--
-- Name: cart_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.cart_items_id_seq', 34, true);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.categories_id_seq', 33, true);


--
-- Name: coupons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.coupons_id_seq', 33, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 33, true);


--
-- Name: order_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.order_items_id_seq', 33, true);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.orders_id_seq', 33, true);


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.products_id_seq', 45, true);


--
-- Name: user_addresses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_addresses_id_seq', 3, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 33, true);


--
-- Name: wishlists_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.wishlists_id_seq', 1, false);


--
-- Name: brands brands_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.brands
    ADD CONSTRAINT brands_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cart_items cart_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_pkey PRIMARY KEY (id);


--
-- Name: cart_items cart_items_user_id_product_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_user_id_product_id_unique UNIQUE (user_id, product_id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_unique UNIQUE (slug);


--
-- Name: coupons coupons_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.coupons
    ADD CONSTRAINT coupons_code_unique UNIQUE (code);


--
-- Name: coupons coupons_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.coupons
    ADD CONSTRAINT coupons_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products products_sku_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_sku_unique UNIQUE (sku);


--
-- Name: products products_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_slug_unique UNIQUE (slug);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: user_addresses user_addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_addresses
    ADD CONSTRAINT user_addresses_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_username_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_unique UNIQUE (username);


--
-- Name: wishlists wishlists_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_pkey PRIMARY KEY (id);


--
-- Name: wishlists wishlists_user_id_product_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_user_id_product_id_unique UNIQUE (user_id, product_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: cart_items cart_items_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: cart_items cart_items_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: orders orders_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: products products_brand_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_brand_id_foreign FOREIGN KEY (brand_id) REFERENCES public.brands(id) ON DELETE CASCADE;


--
-- Name: products products_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: user_addresses user_addresses_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_addresses
    ADD CONSTRAINT user_addresses_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: wishlists wishlists_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: wishlists wishlists_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--


