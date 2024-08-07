import type { Metadata } from "next";
import Meta from "@/components/Meta";
import Header from "@/components/Header";
import TopNavbar from "@/components/TopNavbar";
import '../app/globals.css'

export const metadata: Metadata = {
    title: "Create Next App",
    description: "Generated by create next app",
};

export default function Layout({
    children,
    mainClass,
}: Readonly<{
    children: React.ReactNode;
    mainClass: any;
}>) {
    return (
        <div id="app">
            <Meta />
            <Header>
                <TopNavbar  />
            </Header>
            <main className={mainClass}>{children}</main>
            {/* <Footer></Footer>
            <BottomNavbar toggleSideMenu={toggleSideMenu} />
            <SideMenu
                toggleSideMenu={toggleSideMenu}
                innerRef={sideMenuHook.ref}
                isActive={sideMenuHook.isComponentActive}
            /> */}
        </div>
    );
}
