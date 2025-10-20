import React, { useState } from 'react';
import filmProp from '../card/card.prop';
import reviewProp from '../review/review.prop';
import { FilmTabsNames } from '../../../const';
import PropTypes from 'prop-types';
import FilmTabsList from '../film-tabs-list/film-tabs-list';
import FilmTabOverview from '../film-tab-overview/film-tab-overview';
import FilmTabDetails from '../film-tab-details/film-tab-details';
import FilmTabReviews from '../film-tab-reviews/film-tab-reviews';

const renderFilmTabs = (film, reviews, activeTab) => {
  switch(activeTab) {
    case FilmTabsNames.DETAILS:
      return (
        <FilmTabDetails
          film={film}
        />
      );

    case FilmTabsNames.REVIEWS:
      return (
        <FilmTabReviews
          reviews={reviews}
        />
      );

    default:
      return (
        <FilmTabOverview
          film={film}
        />
      );
  }
};

function FilmTabs(props) {
  const [activeTab, setAtiveTab] = useState(FilmTabsNames.OVERVIEW);
  const {film, reviews}  = props;

  return (
    <div className="film-card__desc">
      <nav className="film-nav film-card__nav">

        <FilmTabsList
          activeTab={activeTab}
          tabClickHandler={(tab) => {
            setAtiveTab(tab);
          }}
        />
      </nav>

      {renderFilmTabs(film, reviews, activeTab)}

    </div>
  );
}

FilmTabs.propTypes = {
  film: filmProp,
  reviews: PropTypes.arrayOf(reviewProp).isRequired,
};

export default FilmTabs;
