import Layout from "@/components/Layout";
import Container from "@/components/Container";
import Head from 'next/head';
import {Avatar} from "@nextui-org/avatar";
import {Input} from "@nextui-org/input";
import Image from 'next/image';
import React from 'react';
import { useRef } from 'react';
import { GetServerSideProps } from "next";
import { getSession } from "@/lib/session";
import { UserData } from "@/lib/models/User";

export const getServerSideProps: GetServerSideProps = async ({ req, res }) => {
    const session = await getSession(req, res);
  
    if (!session.user) {
      return {
        redirect: {
          destination: '/login', // 如果未登录，重定向到登录页面
          permanent: false,
        },
      };
    }
  
    return {
      props: { user: session.user }, // 如果已登录，将用户数据传递给页面组件
    };
  };

export default function AddProduct({ user }: { user: UserData }) {
    const fileInputRef = useRef<HTMLInputElement | null>(null);

    const handleButtonClick = () => {
      if (fileInputRef.current) {
        fileInputRef.current.click();
      }
    };

    return (
        <Layout mainClass="flex w-full h-auto items-center justify-center" user={user}>
            <Head>
                <title>新增商品</title>
            </Head>
            <Container containerClass="flex flex-col max-w-screen-xl  w-full px-6 sm:flex-row">
                <article className="w-full text-wrap p-5 sm:w-1/5 sm:mt-6">
                    <div className="font-medium flex items-center">
                        <button className="p-2">
                            <p>新增商品</p>
                        </button>
                    </div>
                </article>
                <div className="w-full border rounded-xl p-5 bg-white gap-3 sm:w-4/5 sm:p-8 sm:mt-6">
                    <p className="text-3xl font-bold mb-5 mx-4 mt-4">新增商品</p>
                    <hr />
                    <div className="flex flex-col sm:flex-row justify-center pt-4 sm:p-10">
                        <div className="w-full">
                            <form className="mx-4" method="POST" >
                                {/* @csrf */}
                                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-4 sm:items-center sm:grid sm:grid-cols-12 ">
                                     <label htmlFor="product_name" className="items-center sm:text-right col-span-2">Last name</label>
                                     <input type="text" id="product_name" className="col-span-10 bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5" placeholder="請填入商品名稱" required />
                                </div>
                                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-4 sm:items-center sm:grid sm:grid-cols-12 ">
                                     <label htmlFor="product_category_id" className="items-center sm:text-right col-span-2">product_category_id</label>
                                     <input type="text" id="product_category_id" className="col-span-10 bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5" placeholder="product_category_id" required />
                                </div>
                                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-4 sm:items-center sm:grid sm:grid-cols-12 ">
                                     <label htmlFor="tags" className="items-center sm:text-right col-span-2">tags</label>
                                     <input type="text" id="tags" className="col-span-10 bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5" placeholder="tags" required />
                                </div>
                                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-4 sm:items-center sm:grid sm:grid-cols-12 ">
                                     <label htmlFor="summary" className="items-center sm:text-right col-span-2">summary</label>
                                     <input type="text" id="summary" className="col-span-10 bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5" placeholder="summary" required />
                                </div>
                                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-4 sm:items-center sm:grid sm:grid-cols-12 ">
                                     <label htmlFor="description" className="items-center sm:text-right col-span-2">description</label>
                                     <input type="text" id="description" className="col-span-10 bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5" placeholder="description" required />
                                </div>
                                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-4 sm:items-center sm:grid sm:grid-cols-12 ">
                                     <label htmlFor="cover" className="items-center sm:text-right col-span-2">cover</label>
                                     <input type="text" id="cover" className="col-span-10 bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5" placeholder="cover" required />
                                </div>
                                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-4 sm:items-center sm:grid sm:grid-cols-12 ">
                                     <label htmlFor="pictures" className="items-center sm:text-right col-span-2">pictures</label>
                                     <input type="text" id="pictures" className="col-span-10 bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5" placeholder="pictures" required />
                                </div>

                                <div className="flex-col flex justify-center mb-0 sm:mt-8">
                                <button type="submit" className="bg-blue-500 text-white py-2 px-4 rounded">
                                    新增商品
                                </button>
                                </div>
                            </form>
                        </div>
                       
                    </div>
                </div>
            </Container>
            
        </Layout>
    );
}
