import { useRouter } from 'next/router';
import AddProduct from '@/pages/add-product';
import { GetServerSideProps } from "next";
import { getSession } from "@/lib/session";
import { UserData } from "@/lib/models/User";
import { ProductInfo, Types } from "@/lib/models/Product";
import useSWRMutation from 'swr/mutation';
import { getFetcher } from "@/lib/api";
import React, { useRef, useEffect, useState } from 'react';
import { config } from 'process';

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

export default function AppProductPage({ user, api_token }: { user: UserData, api_token: string }) {
  const router = useRouter();
  const { productId } = router.query;
  const { trigger: productInfoTrigger, data: productInfoData } = useSWRMutation(`/backed/api/seller/products/${productId}`, getFetcher);
  const { trigger: typeOptionsTrigger, data: typeOptionsData } = useSWRMutation(`/backed/api/product-type-options/${productId}`, getFetcher);
  useEffect(() => {
    productInfoTrigger(api_token);
    typeOptionsTrigger(api_token);
  }, [productId, api_token]);

  useEffect(() => {
    if (productInfoData) {
      console.log('Product Info Data:', productInfoData.product);
    }
  }, [productInfoData]);

  const [formattedData, setFormattedData] = useState<Types>({ types: [] });
  useEffect(() => {
    if (typeOptionsData) {
      const data = {
        types: typeOptionsData
      };
      setFormattedData(data); 
      console.log('Type Options Data:', data);
    }
  }, [typeOptionsData]);


  return (
    <>
      {productInfoData ? (
        typeOptionsData ? (
          <AddProduct
            user={user} api_token={api_token} product_id={parseInt(productId as string, 10)}
            product_info={productInfoData.product} product_types={formattedData}
          />
        ) : (
          <AddProduct user={user} api_token={api_token} product_id={parseInt(productId as string, 10)} product_info={productInfoData.product} />
        )
      ) : (
        <AddProduct user={user} api_token={api_token} product_id={parseInt(productId as string, 10)} />
      )}
    </>
  );

}
