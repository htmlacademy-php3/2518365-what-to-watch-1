import React from 'react';
import reviewProp from '../review/review.prop';
import PropTypes from 'prop-types';
import Review from '../review/review';

function FilmTabReviews(props) {
  const {reviews} = props;
  const middleIndex = Math.ceil(reviews.length / 2);

  return (
    <div className='film-card__reviews film-card__row'>
      <div className='film-card__reviews-col'>
        {
          reviews.slice(0, middleIndex).map((review) => (
            <Review
              key={review.id}
              review={review}
            />
          ))
        }
      </div>

      <div className='film-card__reviews-col'>
        {
          reviews.slice(middleIndex, reviews.length - 1).map((review) => (
            <Review
              key={review.id}
              review={review}
            />
          ))
        }
      </div>
    </div>
  );
}

FilmTabReviews.propTypes = {
  reviews: PropTypes.arrayOf(reviewProp).isRequired,
};

export default FilmTabReviews;
