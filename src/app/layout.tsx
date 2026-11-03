import type { Metadata } from "next";
import { Inter, Playfair_Display } from "next/font/google";
import "./globals.css";
import { CartProvider } from "@/context/CartContext";
import Navbar from "@/components/Navbar";
import CartDrawer from "@/components/CartDrawer";
import SkinQuizModal from "@/components/SkinQuizModal";
import Footer from "@/components/Footer";

const inter = Inter({
  subsets: ["latin", "vietnamese"],
  variable: "--font-inter",
});

const playfair = Playfair_Display({
  subsets: ["latin", "vietnamese"],
  variable: "--font-playfair",
});

export const metadata: Metadata = {
  title: "Lumina Beauty | Mỹ Phẩm & Chăm Sóc Da Cao Cấp Chính Hãng",
  description:
    "Lumina Beauty - Thiên đường mỹ phẩm, dưỡng da Niacinamide, BHA, Retinol, Hyaluronic Acid và nước hoa cao cấp chính hãng.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="vi" className={`${inter.variable} ${playfair.variable}`}>
      <body className="font-sans antialiased min-h-screen flex flex-col bg-[#FDFBF7] text-stone-800">
        <CartProvider>
          <Navbar />
          <CartDrawer />
          <SkinQuizModal />
          <main className="flex-1">{children}</main>
          <Footer />
        </CartProvider>
      </body>
    </html>
  );
}
