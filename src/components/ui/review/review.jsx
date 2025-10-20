import React from 'react';
import reviewProp from './review.prop';
import { transformRating, humanizeDate } from '../../../utils/utils';

function Review(props) {
  const {userName, rating, comment, date} = props.review;

  return (
    <div className="review">
      <blockquote className="review__quote">
        <p className="review__text">{comment}</p>

        <footer className="review__details">
          <cite className="review__author">{userName}</cite>
          <time
            className="review__date"
            dateTime={humanizeDate(date, 'YYYY-MM-DD')}
          >
            {humanizeDate(date, 'MMMM DD, YYYY')}
          </time>
        </footer>
      </blockquote>
      <div className="review__rating">{transformRating(rating)}</div>
    </div>
  );
}

Review.propTypes = {
  review: reviewProp,
};

export default Review;
