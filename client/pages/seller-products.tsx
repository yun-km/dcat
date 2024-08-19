import Layout from "@/components/Layout";
import Container from "@/components/Container";
import Head from 'next/head';
import React, { useState, useRef } from 'react';
import { GetServerSideProps } from "next";
import { getSession } from "@/lib/session";
import { UserData } from "@/lib/models/User";
import { useEffect } from 'react';
import useSWRMutation from 'swr/mutation';
import { getFetcher } from "@/lib/api";
import { useRouter } from 'next/router';
import { ProductInventories, Types, InventoryEntries, InventoryEntry, OptionSelection, Option } from "@/lib/models/Product";
const fetcher = (url: string) => fetch(url).then(res => res.json());

export const getServerSideProps: GetServerSideProps = async ({ req, res }) => {
    const session = await getSession(req, res);

    if (!session.user) {
        return {
            redirect: {
                destination: '/login',
                permanent: false,
            },
        };
    }

    return {
        props: { user: session.user, api_token: session.api_token },
    };
};


export default function SellerProduct({ user, api_token }: { user: UserData, api_token: string }) {
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage] = useState(10);
    const [searchTerm, setSearchTerm] = useState('');

    const { trigger: productsTrigger, data: productsData } = useSWRMutation(
        `/backed/api/seller/products-inventories?page=${currentPage}&per_page=${perPage}&search=${searchTerm}`,
        getFetcher
    );
    useEffect(() => {
        productsTrigger(api_token);
    }, []);
    useEffect(() => {
        console.log(`Fetching data for page: ${currentPage}, per_page: ${perPage}`);
        productsTrigger(api_token);
    }, [currentPage, perPage, api_token]);


    const [showTbody, setShowTbody] = useState<{ [key: number]: boolean }>({});

    const toggleTbody = (productId: number) => {
        setShowTbody((prevState) => ({
            ...prevState,
            [productId]: !prevState[productId],
        }));
    };

    const router = useRouter();
    const handleEditClick = (productId: number) => {
        router.push(`/seller/product/${productId}`);
    };

    const handleNextPage = () => {
        if (productsData && productsData[1].current_page < productsData[1].last_page) {
            console.log('Next page:', currentPage + 1);
            setCurrentPage(prev => prev + 1);
        } else {
            console.log('Next page disabled');
        }
    };

    const handlePreviousPage = () => {
        if (productsData && productsData[1].current_page > 1) {
            setCurrentPage(prev => prev - 1);
        }
    };

    const handlePerPageChange = (event: React.ChangeEvent<HTMLSelectElement>) => {
        setPerPage(parseInt(event.target.value, 10));
        setCurrentPage(1);
    };

    const handlePageClick = (page: number) => {
        setCurrentPage(page);
    };

    const handleSearchSubmit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setCurrentPage(1);  
        productsTrigger(api_token);  
    };
    const handleSearchChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setSearchTerm(event.target.value);
    };

    return (
        <Layout mainClass="flex w-full h-auto items-center justify-center" user={user}>
            <Head>
                <title>個人資訊</title>
            </Head>
            <Container containerClass="flex flex-col max-w-screen-2xl  w-full px-6 sm:flex-row">
                <article className="w-full text-wrap p-5 sm:w-1/5 sm:mt-6">
                    <div className="font-medium flex items-center">
                        <button className="p-2">
                            <p>您的商品</p>
                            <h1>Welcome, {user.name}</h1>
                        </button>
                    </div>
                </article>
                <div className="w-full border rounded-xl p-5 bg-white gap-3 sm:p-8 sm:mt-6">
                    <p className="text-center text-3xl font-bold mb-5 mx-4 mt-4">您的商品</p>
                    <hr />
                    
                    <form onSubmit={handleSearchSubmit} className="flex items-left max-w-full p-3 justify-start gap-4">
                        <label htmlFor="simple-search" className="sr-only">Search</label>
                        <div className="relative w-full">
                            <div className="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg className="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                id="simple-search"
                                value={searchTerm}
                                onChange={handleSearchChange}
                                className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-sky-500 dark:focus:border-sky-500"
                                placeholder="Search branch name..."
                                required
                            />
                        </div>
                        <button type="submit" className="p-2.5 ms-2 text-sm font-medium text-white bg-sky-700 rounded-lg border border-sky-700 hover:bg-sky-800 focus:ring-4 focus:outline-none focus:ring-sky-300 dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-800">
                            <svg className="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                            <span className="sr-only">Search</span>
                        </button>
                        <button
                            type="submit" onClick={() => router.push(`/add-product`)}
                            className="flex justify-center items-center w-32 p-1 text-sm font-medium rounded-lg gap-1 text-sky-700 border border-sky-700 hover:bg-sky-700 hover:text-white"
                        >
                            <svg className="w-auto flex" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 12h14m-7 7V5" />
                            </svg>
                            新增商品
                        </button>
                    </form>
                    <div className="flex flex-col sm:flex-row justify-center  border rounded-xl">
                        <table className="w-full text-sm text-left rtl:text-right text-gray-500 bg-transparent">
                            <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" className="px-10 py-3 max-w-full">
                                        商品
                                    </th>

                                    <th scope="col" className="px-6 py-3 w-24 text-center">
                                        操作
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {productsData &&
                                    productsData[0].map((productInventories: ProductInventories) => (
                                        <tr key={productInventories.product.id} className="border-b text-center">

                                            <td className="flex">
                                                <table className=" w-full text-gray-500  p-5 m-5">
                                                    <caption className="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900">
                                                        <div className="flex justify-between flex-row \">
                                                            <div className="flex flex-row gap-5">
                                                                <img src={`/backed/images/products/${productInventories.product.cover}`} alt="Product Cover" className="w-10" />
                                                                <div className="justify-center flex flex-col">
                                                                    {productInventories.product.title}
                                                                    <p className="mt-1 text-sm font-normal text-gray-500">
                                                                        {productInventories.product.summary}
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div className="content-center">
                                                                <button
                                                                    onClick={() => toggleTbody(productInventories.product.id)}
                                                                    className=""
                                                                >
                                                                    {showTbody[productInventories.product.id] ? <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                                                                    </svg>
                                                                        : <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                                                                            <path strokeLinecap="round" strokeLinejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                                                                        </svg>
                                                                    }
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </caption>
                                                    {showTbody[productInventories.product.id] && (
                                                        <thead className="text-gray-700 uppercase bg-gray-100 shadow-md">
                                                            <tr>
                                                                <th className="w-1/2 px-4 py-2 rounded-s-lg">規格</th>
                                                                <th className="w-1/4 px-4 py-2">價格</th>
                                                                <th className="w-1/4 px-4 py-2 rounded-e-lg">數量</th>
                                                            </tr>
                                                        </thead>
                                                    )}
                                                    {showTbody[productInventories.product.id] && (
                                                        <tbody>
                                                            {Array.isArray(productInventories.inventories) ? productInventories.inventories.map((inventory: InventoryEntry, index: number) => (

                                                                <tr key={index} className={`px-4 py-2 ${index !== productInventories.inventories.length - 1 ? 'border-b' : ''}`}>
                                                                    <td className="px-4 py-2 text-left">
                                                                        {inventory.productItemTypeOptionId.map((detail: any, idx: number) => (
                                                                            <div key={idx}>
                                                                                <strong>{detail.typeName}:</strong> {detail.optionName}
                                                                            </div>
                                                                        ))}
                                                                    </td>
                                                                    <td className="px-4 py-2 text-center">
                                                                        {inventory.price}
                                                                    </td>
                                                                    <td className="px-4 py-2 text-center">
                                                                        {inventory.totalQuantity}
                                                                    </td>
                                                                </tr>
                                                            )) : []}
                                                        </tbody>
                                                    )}
                                                </table>
                                            </td>

                                            <td className="py-10 align-top">
                                                <div className="flex flex-col gap-3 items-center">
                                                    <button
                                                        onClick={() => handleEditClick(productInventories.product.id)}
                                                        className="text-sky-700 w-auto"
                                                        style={{ writingMode: 'horizontal-tb' }}
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-5">
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>

                                                    </button>
                                                    <button
                                                        onClick={() => handleEditClick(productInventories.product.id)}
                                                        className="text-red-700 w-auto"
                                                        style={{ writingMode: 'horizontal-tb' }}
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-5">
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </div>

                                            </td>

                                        </tr>
                                    ))
                                }

                            </tbody>
                        </table>
                    </div>


                    <div className="pagination-links flex flex-col items-center p-3">
                        <ul className="flex items-center -space-x-px h-8 text-sm">
                            {productsData && productsData[1].links && productsData[1].links.map((link: any, index: number) => (
                                <li>
                                    <button
                                        key={index}
                                        disabled={!link.url}
                                        onClick={() => {
                                            const page = link.url ? Number(new URL(link.url).searchParams.get('page')) : 1;
                                            handlePageClick(page);
                                        }}
                                        className={`p-2 ${link.active ? 'active' : ''} flex items-center justify-center px-3 h-8 ms-0 `}
                                    >
                                        {link.label === "&laquo; Previous" ? (
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                                            </svg>
                                        ) : link.label === "Next &raquo;" ? (
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                                            </svg>
                                        ) : (
                                            link.label
                                        )}
                                    </button>
                                </li>

                            ))}
                        </ul>

                    </div>
                </div>
            </Container>
        </Layout>

    );
}
