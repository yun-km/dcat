export default function Container({ 
    children,
    containerClass,
}: Readonly<{
    children: React.ReactNode;
    containerClass: any;
}>) {
    return(
        <div className={containerClass}>{children}</div>
    );
}
