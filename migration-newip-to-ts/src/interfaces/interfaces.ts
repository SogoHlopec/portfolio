interface ISource {
    id: string;
    name: string;
    description?: string;
    url?: string;
    category?: string;
    language?: string;
    country?: string;
}
interface IArticle {
    source: ISource;
    author: string;
    content: string;
    description: string;
    publishedAt: string;
    title: string;
    url: string;
    urlToImage: string;
}

interface IResp {
    status: string;
    sources?: ISource[];
    totalResults?: number;
    articles?: IArticle[];
}

interface IOptions {
    sources?: string;
    apiKey?: string;
}

export { IResp, ISource, IArticle, IOptions };
