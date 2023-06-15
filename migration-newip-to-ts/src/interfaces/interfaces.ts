export interface ISource {
    id: string;
    name: string;
    description?: string;
    url?: string;
    category?: string;
    language?: string;
    country?: string;
}
export interface IArticle {
    source: ISource;
    author: string;
    content: string;
    description: string;
    publishedAt: string;
    title: string;
    url: string;
    urlToImage: string;
}

export interface IResp {
    status: string;
    sources?: ISource[];
    totalResults?: number;
    articles?: IArticle[];
}

export interface IOptions {
    sources?: string;
    readonly apiKey?: string;
}

export enum Endpoints {
    sources = 'sources',
    everything = 'everything',
}
