/** @type {import('next').NextConfig} */
const host = process.env.HOST || 'http://localhost:8080';

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
    async rewrites() {
      return [
        {
          source: '/backed/api/:path*',
          destination: `${host}/api/:path*`,
          // destination: 'http://xx.xxx.xxx.xxx:8080/:path*',
        },
        {
          source: '/backed/avatars/:path*',
          destination: `${host}/storage/avatars/:path*`,
        },
        {
          source: '/backed/images/:path*',
          destination: `${host}/storage/:path*`,
        },
      ];
    },
  };
  
  export default nextConfig;