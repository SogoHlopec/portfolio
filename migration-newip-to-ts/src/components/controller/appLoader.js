import Loader from './loader';

class AppLoader extends Loader {
    constructor() {
        super('https://newsapi.org/v2/', {
            apiKey: '52e1379f16044025af9e31be8f4a8f9e', // получите свой ключ https://newsapi.org/
        });
    }
}

export default AppLoader;
