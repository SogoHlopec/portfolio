export interface Source {
    id: string;
    name: string;
    category?: string;
    country?: string;
    description?: string;
    language?: string;
    url?: string;
}

export interface Article {
    author: string;
    content: string;
    description: string;
    publishedAt: string;
    source: Source;
    title: string;
    url: string;
    urlToImage: string;
}

export interface Resp {
    articles: Article[];
    status: string;
    totalResults: number;
}

export interface Sources {
    [index: number]: Source;
}
