export interface SourcesNews {
    id: string;
    name: string;
    description: string;
    url: string;
    category: string;
    language: string;
    country: string;
}

export interface Resp {
    status: string;
    sources: SourcesNews[];
}

export interface Source {
    id: string;
    name: string;
}

export interface Article {
    source: Source;
    author: string;
    content: string;
    description: string;
    publishedAt: string;
    title: string;
    url: string;
    urlToImage: string;
}

export interface RespArticles {
    articles: Article[];
    status: string;
    totalResults: number;
}
