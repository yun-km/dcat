/** @type {import('next').NextConfig} */
const nextConfig = {
    images: {
      remotePatterns: [
        {
          protocol: 'https',
          hostname: 'mdbcdn.b-cdn.net',
          pathname: '/img/Photos/new-templates/bootstrap-registration/**',
        },
      ],
    },
  };
  
  export default nextConfig;