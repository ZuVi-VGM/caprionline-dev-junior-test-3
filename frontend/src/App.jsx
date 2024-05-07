import React, { useEffect, useState } from 'react';
import { Button, Rating, Spinner } from 'flowbite-react';

const App = props => {
  const [movies, setMovies] = useState([]);
  const [loading, setLoading] = useState(true);
  const [orderBy, setOrder] = useState('');
  const [genres, setGenres] = useState([]);
  const [filter, setFilter] = useState('');

  const updateOrder = (e) => {
    const selected = e.target.value; 
    setOrder(selected); //TODO: simplify
  };

  const updateFilter = (e) => {
    const selected = e.target.value;
    setFilter(selected); //TODO: simplify
  };

  const fetchMovies = () => {
    setLoading(true);

    let resource = '';

    if(orderBy !== '')
      resource += '/'+orderBy;
    if(filter !== '')
      resource = '/'+filter+resource;

    return fetch('http://localhost:8000/movies'+resource)
      .then(response => response.json())
      .then(data => {
        setMovies(data);
        setLoading(false);
      });
  }

  const fetchGenres = () => {
    return fetch('http://localhost:8000/genres')
      .then(response => response.json())
      .then(data => {
        setGenres(data);
      });
  }

  useEffect(() => {
    fetchGenres();
    fetchMovies();
  }, []);

  useEffect(() => {
    //fetch movies by current filter
    fetchMovies();
  }, [filter, orderBy]);

  return (
    <Layout>
      <Heading />
      <Nav genres={genres} updateFilter={updateFilter} updateOrder={updateOrder}/>

      <MovieList loading={loading}>
        {movies.map((item, key) => (
          <MovieItem key={key} {...item} />
        ))}
      </MovieList>
    </Layout>
  );  
};

const Layout = props => {
  return (
    <section className="bg-white dark:bg-gray-900">
      <div className="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        {props.children}
      </div>
    </section>
  );
};

const Heading = props => {
  return (
    <div className="mx-auto max-w-screen-sm text-center mb-8 lg:mb-16">
      <h1 className="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
        Movie Collection
      </h1>

      <p className="font-light text-gray-500 lg:mb-16 sm:text-xl dark:text-gray-400">
        Explore the whole collection of movies
      </p>
    </div>
  );
};

const Nav = ({genres, updateFilter, updateOrder}) => {

  return (
    <ul className="flex justify-end mb-4">
      <li className="mr-3">
      <label htmlFor="genres">Choose a Genre:</label>
      <select id="genres" onChange={updateFilter} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" >
        <option default value=''>-</option>
        {genres.map((item, key) => (
          <option key={key} value={item.id}>{item.name}</option>
        ))}
      </select>
      </li>
      <li className="mr-3" >
        <label htmlFor="order">Order By:</label>
        <select id="order" onChange={updateOrder} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <option default value="">-</option>
          <option value="release">Release</option>
          <option value="rating">Rating</option>
        </select>
      </li>
    </ul>
  );
};

const MovieList = props => {
  if (props.loading) {
    return (
      <div className="text-center">
        <Spinner size="xl" />
      </div>
    );
  }

  return (
    <div className="grid gap-4 md:gap-y-8 xl:grid-cols-6 lg:grid-cols-4 md:grid-cols-3">
      {props.children}
    </div>
  );
};

const MovieItem = props => {
  return (
    <div className="flex flex-col w-full h-full rounded-lg shadow-md lg:max-w-sm">
      <div className="grow">
        <img
          className="object-cover w-full h-60 md:h-80"
          src={props.imageUrl}
          alt={props.title}
          loading="lazy"
        />
      </div>

      <div className="grow flex flex-col h-full p-3">
        <div className="grow mb-3 last:mb-0">
          {props.year || props.rating
            ? <div className="flex justify-between align-middle text-gray-900 text-xs font-medium mb-2">
                <span>{props.year}</span>

                {props.rating
                  ? <Rating>
                      <Rating.Star />

                      <span className="ml-0.5">
                        {props.rating}
                      </span>
                    </Rating>
                  : null
                }
              </div>
            : null
          }

          <h3 className="text-gray-900 text-lg leading-tight font-semibold mb-1">
            {props.title}
          </h3>

          <p className="text-gray-600 text-sm leading-normal mb-4 last:mb-0">
            {props.plot.substr(0, 80)}...
          </p>
        </div>

        {props.wikipediaUrl
          ? <Button
              color="light"
              size="xs"
              className="w-full"
              onClick={() => window.open(props.wikipediaUrl, '_blank')}
            >
              More
            </Button>
          : null
        }
      </div>
    </div>
  );
};

export default App;
