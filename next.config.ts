import type { NextConfig } from "next";

const isProd = process.env.NODE_ENV === 'production';
const repoName = '/portfolio';

const nextConfig: NextConfig = {
    /* config options here */
    output: 'export',
    trailingSlash: true,
    images: {
        unoptimized: true,
    },
    basePath: isProd ? repoName : '',
    assetPrefix: isProd ? `${repoName}/` : '',
    reactCompiler: true,
};

export default nextConfig;
