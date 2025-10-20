import React, { Fragment } from 'react';
import { BrowserRouter, Route, Switch, Link } from 'react-router-dom';
import {AppRoute} from '../../const';
import PropTypes from 'prop-types';
import Main from '../pages/main/main';
import SignIn from '../pages/signin/signin';
import MyList from '../pages/mylist/mylist';
import Film from '../pages/film/film';
import Review from '../ui/review/review';
import Player from '../pages/player/player';
import filmProp from '../ui/card/card.prop';
import reviewProp from '../ui/review/review.prop';
import {getFilm} from '../../utils/utils';


function App(props) {
  const {films,reviews, name, genre, year} = props;
  return (
    <BrowserRouter>
      <Switch>
        <Route path="/" exact >
          <Main films={films} name={name} genre={genre} year={year} />
        </Route>
        <Route path="/login" exact component={SignIn} />
        <Route path="/mylist" exact >
          <MyList films={films} />
        </Route>
        <Route
          exact path={`${AppRoute.FILM}/:id`}
          render={(data) => (
            <Film
              film={getFilm(films, data.match.params.id)}
              films={films}
              reviews={reviews}
            />)}
        />
        <Route
          exact path={`${AppRoute.FILM}/:id/review`}
          render={(data) => (
            <Review
              film={getFilm(films, data.match.params.id)}
            />)}
        />
        <Route
          exact path={`${AppRoute.PLAYER}/:id`}
          render={(data) => (
            <Player
              film={getFilm(films, data.match.params.id)}
            />
          )}
        />
        <Route
          render={() => (
            <Fragment>
              <h1>
                404.
                <br />
                <small>Page not found</small>
              </h1>
              <Link to="/">Go to main page</Link>
            </Fragment>
          )}
        />
      </Switch>
    </BrowserRouter>
  );
}

App.propTypes = {
  films: PropTypes.arrayOf(filmProp).isRequired,
  reviews: PropTypes.arrayOf(reviewProp).isRequired,
  name: PropTypes.string.isRequired,
  genre: PropTypes.string.isRequired,
  year: PropTypes.number.isRequired,
};

export default App;
